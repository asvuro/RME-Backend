<?php

namespace Modules\GeneralPatientFamilyIdentityCard\Tests\Feature;

use Tests\TestCase;
use Modules\GeneralPatientFamilyIdentityCard\Models\PatientFamilyIdentityCard;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PatientFamilyIdentityCardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RoleAndPermissionSeeder::class);
        // Rute modul ini kini dilindungi auth:sanctum (fix temuan security
        // review K-1) - semua request test harus terautentikasi.
        $user = \Modules\Auth\Models\User::factory()->create();
        $user->assignRole('petugas');
        $this->actingAs($user, 'sanctum');
    }

    public function test_can_list_patient_family_identity_cards()
    {
        PatientFamilyIdentityCard::factory()->count(3)->create();
        $response = $this->getJson('/api/v1/patientfamilyidentitycards');
        $response->assertStatus(200)->assertJsonCount(3, 'data');
    }

    public function test_can_create_patient_family_identity_card()
    {
        $data = PatientFamilyIdentityCard::factory()->make()->toArray();
        $response = $this->postJson('/api/v1/patientfamilyidentitycards', $data);
        $response->assertStatus(201);
        $this->assertDatabaseHas('patient_family_identity_cards', ['identity_number' => $data['identity_number']]);
    }

    public function test_can_show_patient_family_identity_card()
    {
        $model = PatientFamilyIdentityCard::factory()->create();
        $response = $this->getJson("/api/v1/patientfamilyidentitycards/{$model->id}");
        $response->assertStatus(200)->assertJsonPath('data.identity_number', $model->identity_number);
    }

    public function test_can_update_patient_family_identity_card()
    {
        $model = PatientFamilyIdentityCard::factory()->create();
        $response = $this->putJson("/api/v1/patientfamilyidentitycards/{$model->id}", [
            'patient_family_id' => $model->patient_family_id,
            'identity_type' => 'SIM',
            'identity_number' => '1234567890',
            'is_active' => true,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('patient_family_identity_cards', ['identity_number' => '1234567890']);
    }

    public function test_can_delete_patient_family_identity_card()
    {
        $model = PatientFamilyIdentityCard::factory()->create();
        $response = $this->deleteJson("/api/v1/patientfamilyidentitycards/{$model->id}");
        $response->assertStatus(204);
        $this->assertDatabaseMissing('patient_family_identity_cards', ['id' => $model->id]);
    }
}
