<?php

namespace Modules\GeneralMedicationAdministrationType\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralMedicationAdministrationType\Models\MedicationAdministrationType;
use Tests\TestCase;

class MedicationAdministrationTypeControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_medication_administration_type(): void
    {
        $this->actingUser();
        MedicationAdministrationType::factory()->count(3)->create();

        $this->getJson('/api/v1/medication-administration-types')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_medication_administration_type(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/medication-administration-types', ['name' => 'Contoh Jenispemberianobat', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Jenispemberianobat');

        $this->assertDatabaseHas('medication_administration_types', ['name' => 'Contoh Jenispemberianobat']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        MedicationAdministrationType::factory()->create(['name' => 'Contoh Jenispemberianobat']);

        $this->postJson('/api/v1/medication-administration-types', ['name' => 'Contoh Jenispemberianobat'])->assertStatus(422);
    }

    public function test_it_deletes_medication_administration_type(): void
    {
        $this->actingUser();
        $medicationAdministrationType = MedicationAdministrationType::factory()->create();

        $this->deleteJson("/api/v1/medication-administration-types/{$medicationAdministrationType->id}")->assertStatus(204);
        $this->assertDatabaseMissing('medication_administration_types', ['id' => $medicationAdministrationType->id]);
    }
}