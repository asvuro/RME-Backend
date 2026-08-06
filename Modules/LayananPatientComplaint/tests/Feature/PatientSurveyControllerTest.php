<?php

namespace Modules\LayananPatientComplaint\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\LayananPatientComplaint\Models\PatientSurvey;
use Modules\PendaftaranVisit\Models\Visit;
use Tests\TestCase;

/**
 * Feature test survei kepuasan pasien: CRUD endpoint dan gerbang
 * "satu kunjungan satu survei" di PatientSurveyService.
 */
class PatientSurveyControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }

    private function actingUser(): User
    {
        $user = User::factory()->create();
        $user->assignRole('petugas');
        $this->actingAs($user, 'sanctum');

        return $user;
    }

    public function test_petugas_dapat_menyimpan_survei(): void
    {
        $this->actingUser();
        $visit = Visit::factory()->create();

        $this->postJson('/api/v1/patient-surveys', [
            'visit_id' => $visit->id,
            'satisfaction_score' => 4,
            'feedback_text' => 'Pelayanan memuaskan.',
            'submitted_at' => now()->toDateTimeString(),
        ])
            ->assertCreated()
            ->assertJsonPath('satisfaction_score', 4);

        $this->assertDatabaseHas('patient_surveys', [
            'visit_id' => $visit->id,
            'satisfaction_score' => 4,
        ]);
    }

    public function test_skor_di_luar_rentang_1_sampai_5_ditolak(): void
    {
        $this->actingUser();
        $visit = Visit::factory()->create();

        $this->postJson('/api/v1/patient-surveys', [
            'visit_id' => $visit->id,
            'satisfaction_score' => 6,
            'submitted_at' => now()->toDateTimeString(),
        ])->assertStatus(422);

        $this->assertSame(0, PatientSurvey::count());
    }

    public function test_satu_kunjungan_hanya_boleh_mengisi_survei_satu_kali(): void
    {
        $this->actingUser();
        $visit = Visit::factory()->create();

        $this->postJson('/api/v1/patient-surveys', [
            'visit_id' => $visit->id,
            'satisfaction_score' => 5,
            'submitted_at' => now()->toDateTimeString(),
        ])->assertCreated();

        // Pengisian kedua untuk kunjungan yang sama ditolak gerbang service.
        $this->postJson('/api/v1/patient-surveys', [
            'visit_id' => $visit->id,
            'satisfaction_score' => 3,
            'submitted_at' => now()->toDateTimeString(),
        ])->assertStatus(422);

        $this->assertSame(1, PatientSurvey::count());
    }

    public function test_daftar_survei_tampil_dan_bisa_diperbarui_skornya(): void
    {
        $this->actingUser();
        PatientSurvey::factory()->count(2)->create();

        $this->getJson('/api/v1/patient-surveys')
            ->assertOk()
            ->assertJsonCount(2, 'data');

        $survei = PatientSurvey::query()->first();

        $this->patchJson("/api/v1/patient-surveys/{$survei->id}", [
            'satisfaction_score' => 5,
        ])->assertOk()->assertJsonPath('satisfaction_score', 5);

        $this->assertSame(5, $survei->refresh()->satisfaction_score);
    }
}
