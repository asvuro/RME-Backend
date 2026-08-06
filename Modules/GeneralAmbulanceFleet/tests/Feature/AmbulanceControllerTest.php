<?php

namespace Modules\GeneralAmbulanceFleet\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralAmbulanceFleet\Models\Ambulance;
use Tests\TestCase;

class AmbulanceControllerTest extends TestCase
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

    public function test_it_lists_ambulances(): void
    {
        $this->actingUser();
        Ambulance::factory()->count(3)->create();

        $this->getJson('/api/v1/ambulances')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_filters_ambulances_by_status(): void
    {
        $this->actingUser();
        Ambulance::factory()->count(2)->create();
        Ambulance::factory()->maintenance()->create();

        $this->getJson('/api/v1/ambulances?status=maintenance')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_petugas_can_register_ambulance_and_it_starts_available(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/ambulances', [
            'vehicle_code' => 'AMB-0001',
            'plate_number' => 'B 1234 ABC',
        ])->assertCreated()->assertJsonPath('status', Ambulance::STATUS_AVAILABLE);

        // Gerbang: meski payload mencoba menyelundupkan status, armada baru
        // selalu available - in_use/maintenance hanya lahir dari kejadian bisnis.
        $this->postJson('/api/v1/ambulances', [
            'vehicle_code' => 'AMB-0002',
            'plate_number' => 'B 5678 XYZ',
            'status' => Ambulance::STATUS_IN_USE,
        ])->assertCreated();

        $this->assertDatabaseHas('ambulances', [
            'vehicle_code' => 'AMB-0002',
            'status' => Ambulance::STATUS_AVAILABLE,
        ]);
    }

    public function test_status_cannot_be_set_manually_to_in_use(): void
    {
        $this->actingUser();
        $ambulance = Ambulance::factory()->create();

        // 'in_use' hanya boleh lahir dari mulai trip; tanpa gerbang ini petugas
        // bisa mengunci ambulans tanpa trip dan melewati start()/complete().
        $this->patchJson("/api/v1/ambulances/{$ambulance->id}", [
            'status' => Ambulance::STATUS_IN_USE,
        ])->assertStatus(422);

        // Transisi manual legal: available <-> maintenance.
        $this->patchJson("/api/v1/ambulances/{$ambulance->id}", [
            'status' => Ambulance::STATUS_MAINTENANCE,
        ])->assertOk();

        $this->assertDatabaseHas('ambulances', [
            'id' => $ambulance->id,
            'status' => Ambulance::STATUS_MAINTENANCE,
        ]);
    }
}
