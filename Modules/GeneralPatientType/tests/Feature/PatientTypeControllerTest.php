<?php

namespace Modules\GeneralPatientType\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralPatientType\Models\PatientType;
use Tests\TestCase;

class PatientTypeControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_patient_type(): void
    {
        $this->actingUser();
        PatientType::factory()->count(3)->create();

        $this->getJson('/api/v1/patient-types')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_patient_type(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/patient-types', ['name' => 'Contoh Jenispasien', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Jenispasien');

        $this->assertDatabaseHas('patient_types', ['name' => 'Contoh Jenispasien']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        PatientType::factory()->create(['name' => 'Contoh Jenispasien']);

        $this->postJson('/api/v1/patient-types', ['name' => 'Contoh Jenispasien'])->assertStatus(422);
    }

    public function test_it_deletes_patient_type(): void
    {
        $this->actingUser();
        $patientType = PatientType::factory()->create();

        $this->deleteJson("/api/v1/patient-types/{$patientType->id}")->assertStatus(204);
        $this->assertDatabaseMissing('patient_types', ['id' => $patientType->id]);
    }
}