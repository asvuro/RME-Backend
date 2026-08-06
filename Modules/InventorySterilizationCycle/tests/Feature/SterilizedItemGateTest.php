<?php

namespace Modules\InventorySterilizationCycle\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\InventorySterilizationCycle\Models\SterilizationCycle;
use Tests\TestCase;

class SterilizedItemGateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }

    private function actingUser(): void
    {
        $user = User::factory()->create();
        $user->assignRole('petugas');
        $this->actingAs($user, 'sanctum');
    }

    public function test_it_rejects_sterilized_item_when_cycle_still_in_process(): void
    {
        $this->actingUser();
        $cycle = SterilizationCycle::factory()->create([
            'status' => SterilizationCycle::STATUS_IN_PROCESS,
            'biological_indicator_result' => SterilizationCycle::BI_PENDING,
        ]);

        $this->postJson('/api/v1/sterilized-items', [
            'cycle_id' => $cycle->id,
            'item_name' => 'Set Instrumen Bedah',
            'quantity' => 5,
        ])->assertStatus(422);
    }

    public function test_it_rejects_sterilized_item_when_biological_indicator_not_negative(): void
    {
        $this->actingUser();
        $cycle = SterilizationCycle::factory()->create([
            'status' => SterilizationCycle::STATUS_PASSED,
            'biological_indicator_result' => SterilizationCycle::BI_POSITIVE,
            'completed_at' => now(),
        ]);

        $this->postJson('/api/v1/sterilized-items', [
            'cycle_id' => $cycle->id,
            'item_name' => 'Set Instrumen Bedah',
            'quantity' => 5,
        ])->assertStatus(422);
    }

    /**
     * Gerbang utama modul: cycle passed + BI negatif -> boleh dibuat, dan
     * expiry_date = completed_at + shelf_life default (30 hari, karena
     * key 'cssd.default_shelf_life_days' belum diisi HospitalConfig).
     */
    public function test_it_creates_sterilized_item_and_computes_expiry_when_cycle_passed_and_bi_negative(): void
    {
        $this->actingUser();
        $completedAt = now()->subDay();
        $cycle = SterilizationCycle::factory()->create([
            'status' => SterilizationCycle::STATUS_PASSED,
            'biological_indicator_result' => SterilizationCycle::BI_NEGATIVE,
            'completed_at' => $completedAt,
        ]);

        $response = $this->postJson('/api/v1/sterilized-items', [
            'cycle_id' => $cycle->id,
            'item_name' => 'Set Instrumen Bedah',
            'quantity' => 5,
        ]);

        $response->assertCreated();
        $expectedExpiry = $completedAt->copy()->addDays(30)->toDateString();
        $this->assertSame($expectedExpiry, substr($response->json('expiry_date'), 0, 10));
    }

    public function test_check_expiry_endpoint_reports_expired_item(): void
    {
        $this->actingUser();
        $cycle = SterilizationCycle::factory()->create([
            'status' => SterilizationCycle::STATUS_PASSED,
            'biological_indicator_result' => SterilizationCycle::BI_NEGATIVE,
            'completed_at' => now()->subDays(40),
        ]);

        $item = $this->postJson('/api/v1/sterilized-items', [
            'cycle_id' => $cycle->id,
            'item_name' => 'Duk Steril',
            'quantity' => 3,
        ])->json();

        $this->getJson("/api/v1/sterilized-items/{$item['id']}/check-expiry")
            ->assertOk()
            ->assertJsonPath('expired', true);
    }
}
