<?php

namespace Modules\PendaftaranPatientEscortIdentityCard\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\PendaftaranPatientEscort\Models\PatientEscort;
use Modules\PendaftaranPatientEscortIdentityCard\Models\PatientEscortIdentityCard;
use Tests\TestCase;

class PatientEscortIdentityCardControllerTest extends TestCase
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

    public function test_it_creates_an_escort_identity_card(): void
    {
        $this->actingUser();
        $escort = PatientEscort::factory()->create();

        $this->postJson('/api/v1/patient-escort-identity-cards', [
            'patient_escort_id' => $escort->id,
            'card_type' => 'KTP',
            'card_number' => '3201234567890099',
        ])
            ->assertCreated()
            ->assertJsonPath('data.card_type', 'KTP');

        $this->assertDatabaseHas('patient_escort_identity_cards', ['patient_escort_id' => $escort->id]);
    }

    public function test_it_rejects_duplicate_card_type_for_same_escort(): void
    {
        $this->actingUser();
        $escort = PatientEscort::factory()->create();
        PatientEscortIdentityCard::factory()->create(['patient_escort_id' => $escort->id, 'card_type' => 'KTP']);

        $this->postJson('/api/v1/patient-escort-identity-cards', [
            'patient_escort_id' => $escort->id,
            'card_type' => 'KTP',
            'card_number' => '3201234567890098',
        ])->assertStatus(422);
    }

    public function test_it_rejects_invalid_card_type(): void
    {
        $this->actingUser();
        $escort = PatientEscort::factory()->create();

        $this->postJson('/api/v1/patient-escort-identity-cards', [
            'patient_escort_id' => $escort->id,
            'card_type' => 'NPWP',
            'card_number' => '123456',
        ])->assertStatus(422);
    }

    public function test_it_deletes_an_escort_identity_card(): void
    {
        $this->actingUser();
        $card = PatientEscortIdentityCard::factory()->create();

        $this->deleteJson("/api/v1/patient-escort-identity-cards/{$card->id}")->assertStatus(204);
        $this->assertDatabaseMissing('patient_escort_identity_cards', ['id' => $card->id]);
    }

    public function test_guest_cannot_access_escort_identity_cards(): void
    {
        $this->getJson('/api/v1/patient-escort-identity-cards')->assertStatus(401);
    }
}
