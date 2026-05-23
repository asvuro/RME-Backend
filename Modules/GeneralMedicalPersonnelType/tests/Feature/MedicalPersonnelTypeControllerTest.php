<?php

namespace Modules\GeneralMedicalPersonnelType\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralMedicalPersonnelType\Models\MedicalPersonnelType;
use Tests\TestCase;

class MedicalPersonnelTypeControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_medical_personnel_type(): void
    {
        $this->actingUser();
        MedicalPersonnelType::factory()->count(3)->create();

        $this->getJson('/api/v1/medical-personnel-types')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_medical_personnel_type(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/medical-personnel-types', ['name' => 'Contoh Jenispetugasmedis', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Jenispetugasmedis');

        $this->assertDatabaseHas('medical_personnel_types', ['name' => 'Contoh Jenispetugasmedis']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        MedicalPersonnelType::factory()->create(['name' => 'Contoh Jenispetugasmedis']);

        $this->postJson('/api/v1/medical-personnel-types', ['name' => 'Contoh Jenispetugasmedis'])->assertStatus(422);
    }

    public function test_it_deletes_medical_personnel_type(): void
    {
        $this->actingUser();
        $medicalPersonnelType = MedicalPersonnelType::factory()->create();

        $this->deleteJson("/api/v1/medical-personnel-types/{$medicalPersonnelType->id}")->assertStatus(204);
        $this->assertDatabaseMissing('medical_personnel_types', ['id' => $medicalPersonnelType->id]);
    }
}