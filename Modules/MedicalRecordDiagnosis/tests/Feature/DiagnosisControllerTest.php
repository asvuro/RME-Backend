<?php

namespace Modules\MedicalRecordDiagnosis\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralDiagnosisCode\Models\DiagnosisCode;
use Modules\MedicalRecordDiagnosis\Models\Diagnosis;
use Modules\PendaftaranVisit\Models\Visit;
use Tests\TestCase;

class DiagnosisControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): User
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        return $user;
    }

    public function test_it_records_a_diagnosis_for_a_visit(): void
    {
        $user = $this->actingUser();
        $visit = Visit::factory()->create();
        $code = DiagnosisCode::factory()->create(['code' => 'A90']);

        $response = $this->postJson('/api/v1/diagnoses', [
            'visit_id' => $visit->id,
            'diagnosis_code_id' => $code->id,
            'is_primary' => true,
        ]);

        $response->assertCreated()->assertJsonPath('data.is_primary', true);
        $this->assertDatabaseHas('diagnoses', ['visit_id' => $visit->id, 'recorded_by' => $user->id]);
    }

    public function test_only_one_primary_diagnosis_per_visit(): void
    {
        $this->actingUser();
        $visit = Visit::factory()->create();
        $first = Diagnosis::factory()->primary()->create(['visit_id' => $visit->id]);

        $this->postJson('/api/v1/diagnoses', [
            'visit_id' => $visit->id,
            'diagnosis_code_id' => DiagnosisCode::factory()->create()->id,
            'is_primary' => true,
        ])->assertCreated();

        $this->assertFalse($first->fresh()->is_primary);
    }

    public function test_it_removes_a_wrongly_added_diagnosis(): void
    {
        $this->actingUser();
        $diagnosis = Diagnosis::factory()->create();

        $this->deleteJson("/api/v1/diagnoses/{$diagnosis->id}")->assertStatus(204);
    }

    public function test_it_rejects_store_without_required_fields(): void
    {
        $this->actingUser();
        Visit::factory()->create();
        DiagnosisCode::factory()->create(['code' => 'A90']);

        $this->postJson('/api/v1/diagnoses', [])->assertStatus(422);
    }

    public function test_it_rejects_store_with_unknown_visit_or_code(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/diagnoses', [
            'visit_id' => 999999,
            'diagnosis_code_id' => 999999,
        ])->assertStatus(422);
    }

    public function test_it_rejects_per_page_above_limit(): void
    {
        $this->actingUser();

        $this->getJson('/api/v1/diagnoses?per_page=500')->assertStatus(422);
    }
}
