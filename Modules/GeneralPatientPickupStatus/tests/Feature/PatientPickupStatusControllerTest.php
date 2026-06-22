<?php

namespace Modules\GeneralPatientPickupStatus\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralPatientPickupStatus\Models\PatientPickupStatus;
use Tests\TestCase;

class PatientPickupStatusControllerTest extends TestCase
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

    public function test_it_lists_patient_pickup_statuse(): void
    {
        $this->actingUser();
        PatientPickupStatus::factory()->count(3)->create();

        $this->getJson('/api/v1/patient-pickup-statuses')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_patient_pickup_status(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/patient-pickup-statuses', ['name' => 'Contoh Statusambilpasien', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Statusambilpasien');

        $this->assertDatabaseHas('patient_pickup_statuses', ['name' => 'Contoh Statusambilpasien']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        PatientPickupStatus::factory()->create(['name' => 'Contoh Statusambilpasien']);

        $this->postJson('/api/v1/patient-pickup-statuses', ['name' => 'Contoh Statusambilpasien'])->assertStatus(422);
    }

    public function test_it_deletes_patient_pickup_status(): void
    {
        $this->actingUser();
        $patientPickupStatus = PatientPickupStatus::factory()->create();

        $this->deleteJson("/api/v1/patient-pickup-statuses/{$patientPickupStatus->id}")->assertStatus(204);
        $this->assertDatabaseMissing('patient_pickup_statuses', ['id' => $patientPickupStatus->id]);
    }
}