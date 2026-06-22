<?php

namespace Modules\LayananMedicalSupplyUsage\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\LayananMedicalSupplyUsage\Models\MedicalSupplyUsage;
use Tests\TestCase;

class MedicalSupplyUsageControllerTest extends TestCase
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

    public function test_it_lists_supply_usages(): void
    {
        $this->actingUser();
        MedicalSupplyUsage::factory()->count(3)->create();

        $this->getJson('/api/v1/medical-supply-usages')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_supply_usage(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/medical-supply-usages', [
            'visit_id' => \Modules\PendaftaranVisit\Models\Visit::factory()->create()->id,
            'used_at' => '2026-01-01 08:00:00',
            'status' => 'draft',
        ])->assertCreated();

        $this->assertDatabaseCount('medical_supply_usages', 1);
    }

    public function test_it_shows_supply_usage(): void
    {
        $this->actingUser();
        $supply_usage = MedicalSupplyUsage::factory()->create();

        $this->getJson("/api/v1/medical-supply-usages/{$supply_usage->id}")->assertOk()->assertJsonPath('data.id', $supply_usage->id);
    }

}
