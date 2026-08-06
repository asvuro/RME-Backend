<?php

namespace Modules\InventoryBloodBag\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralPatient\Models\Patient;
use Modules\InventoryBloodBag\Models\BloodBag;
use Modules\InventoryBloodBag\Models\CrossmatchTest;
use Tests\TestCase;

/**
 * Gerbang bisnis utama BDRS: crossmatch hanya boleh dari kantong in_stock,
 * hasil kompatibel mereservasi kantong 48 jam, hasil tidak kompatibel
 * membiarkan kantong tetap in_stock.
 */
class CrossmatchTestControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }

    private function actingUser(): User
    {
        $user = User::factory()->create();
        $user->assignRole('petugas');
        $this->actingAs($user, 'sanctum');

        return $user;
    }

    public function test_compatible_crossmatch_reserves_bag_for_48_hours(): void
    {
        $this->actingUser();
        $bag = BloodBag::factory()->create(['status' => BloodBag::STATUS_IN_STOCK]);
        $patient = Patient::factory()->create();

        $response = $this->postJson("/api/v1/blood-bags/{$bag->id}/crossmatch", [
            'patient_id' => $patient->id,
            'major_result' => CrossmatchTest::RESULT_NEGATIVE,
            'minor_result' => CrossmatchTest::RESULT_NEGATIVE,
            'auto_control' => CrossmatchTest::RESULT_NEGATIVE,
        ]);

        $response->assertCreated()->assertJsonPath('data.is_compatible', true);

        $this->assertSame(BloodBag::STATUS_CROSSMATCH_RESERVED, $bag->fresh()->status);

        $test = CrossmatchTest::first();
        $expectedReservedUntil = $test->tested_at->copy()->addHours(48);
        $this->assertTrue($test->reserved_until->equalTo($expectedReservedUntil));
    }

    public function test_incompatible_crossmatch_keeps_bag_in_stock(): void
    {
        $this->actingUser();
        $bag = BloodBag::factory()->create(['status' => BloodBag::STATUS_IN_STOCK]);
        $patient = Patient::factory()->create();

        $this->postJson("/api/v1/blood-bags/{$bag->id}/crossmatch", [
            'patient_id' => $patient->id,
            'major_result' => CrossmatchTest::RESULT_POSITIVE,
            'minor_result' => CrossmatchTest::RESULT_NEGATIVE,
            'auto_control' => CrossmatchTest::RESULT_NEGATIVE,
        ])->assertCreated()->assertJsonPath('data.is_compatible', false);

        $this->assertSame(BloodBag::STATUS_IN_STOCK, $bag->fresh()->status);
    }

    public function test_crossmatch_rejected_when_bag_not_in_stock(): void
    {
        $this->actingUser();
        $bag = BloodBag::factory()->create(['status' => BloodBag::STATUS_TRANSFUSED]);
        $patient = Patient::factory()->create();

        $this->postJson("/api/v1/blood-bags/{$bag->id}/crossmatch", [
            'patient_id' => $patient->id,
            'major_result' => CrossmatchTest::RESULT_NEGATIVE,
            'minor_result' => CrossmatchTest::RESULT_NEGATIVE,
            'auto_control' => CrossmatchTest::RESULT_NEGATIVE,
        ])->assertStatus(422);
    }

    public function test_release_returns_bag_to_in_stock(): void
    {
        $this->actingUser();
        $bag = BloodBag::factory()->create(['status' => BloodBag::STATUS_CROSSMATCH_RESERVED]);
        $test = CrossmatchTest::factory()->create([
            'blood_bag_id' => $bag->id,
            'reserved_until' => now()->addHours(48),
        ]);

        $this->postJson("/api/v1/crossmatch-tests/{$test->id}/release")
            ->assertOk();

        $this->assertSame(BloodBag::STATUS_IN_STOCK, $bag->fresh()->status);
    }

    public function test_it_lists_crossmatch_tests_filtered_by_blood_bag(): void
    {
        $this->actingUser();
        $bag = BloodBag::factory()->create();
        CrossmatchTest::factory()->count(2)->create(['blood_bag_id' => $bag->id]);
        CrossmatchTest::factory()->create();

        $this->getJson("/api/v1/crossmatch-tests?blood_bag_id={$bag->id}")
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }
}
