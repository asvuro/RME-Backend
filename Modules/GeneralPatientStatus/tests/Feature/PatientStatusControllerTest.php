<?php

namespace Modules\GeneralPatientStatus\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralPatientStatus\Models\PatientStatus;
use Tests\TestCase;

class PatientStatusControllerTest extends TestCase
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

    public function test_it_lists_patient_statuse(): void
    {
        $this->actingUser();
        PatientStatus::factory()->count(3)->create();

        $this->getJson('/api/v1/patient-statuses')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_patient_status(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/patient-statuses', ['name' => 'Contoh Statuspasien', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Statuspasien');

        $this->assertDatabaseHas('patient_statuses', ['name' => 'Contoh Statuspasien']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        PatientStatus::factory()->create(['name' => 'Contoh Statuspasien']);

        $this->postJson('/api/v1/patient-statuses', ['name' => 'Contoh Statuspasien'])->assertStatus(422);
    }

    public function test_it_deletes_patient_status(): void
    {
        $this->actingUser();
        $patientStatus = PatientStatus::factory()->create();

        $this->deleteJson("/api/v1/patient-statuses/{$patientStatus->id}")->assertStatus(204);
        $this->assertDatabaseMissing('patient_statuses', ['id' => $patientStatus->id]);
    }
}