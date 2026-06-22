<?php

namespace Modules\GeneralAccommodationCalculationRule\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralAccommodationCalculationRule\Models\AccommodationCalculationRule;
use Tests\TestCase;

class AccommodationCalculationRuleControllerTest extends TestCase
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

    public function test_it_lists_accommodation_calculation_rule(): void
    {
        $this->actingUser();
        AccommodationCalculationRule::factory()->count(3)->create();

        $this->getJson('/api/v1/accommodation-calculation-rules')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_accommodation_calculation_rule(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/accommodation-calculation-rules', ['name' => 'Contoh Aturanperhitunganakomodasi', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Aturanperhitunganakomodasi');

        $this->assertDatabaseHas('accommodation_calculation_rules', ['name' => 'Contoh Aturanperhitunganakomodasi']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        AccommodationCalculationRule::factory()->create(['name' => 'Contoh Aturanperhitunganakomodasi']);

        $this->postJson('/api/v1/accommodation-calculation-rules', ['name' => 'Contoh Aturanperhitunganakomodasi'])->assertStatus(422);
    }

    public function test_it_deletes_accommodation_calculation_rule(): void
    {
        $this->actingUser();
        $accommodationCalculationRule = AccommodationCalculationRule::factory()->create();

        $this->deleteJson("/api/v1/accommodation-calculation-rules/{$accommodationCalculationRule->id}")->assertStatus(204);
        $this->assertDatabaseMissing('accommodation_calculation_rules', ['id' => $accommodationCalculationRule->id]);
    }
}