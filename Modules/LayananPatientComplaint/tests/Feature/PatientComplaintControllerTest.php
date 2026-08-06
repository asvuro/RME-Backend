<?php

namespace Modules\LayananPatientComplaint\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralEmployee\Models\Employee;
use Modules\GeneralPatient\Models\Patient;
use Modules\LayananPatientComplaint\Models\PatientComplaint;
use Tests\TestCase;

/**
 * Feature test komplain pasien: CRUD endpoint, state machine
 * baru -> diproses -> selesai di PatientComplaintService, dan rekap summary.
 */
class PatientComplaintControllerTest extends TestCase
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

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'patient_id' => Patient::factory()->create()->id,
            'category' => 'pelayanan',
            'description' => 'Waktu tunggu pendaftaran terlalu lama.',
            'submitted_at' => now()->toDateTimeString(),
        ], $overrides);
    }

    public function test_petugas_dapat_menyimpan_komplain_dan_status_lahir_sebagai_baru(): void
    {
        $this->actingUser();

        $payload = $this->validPayload();

        $this->postJson('/api/v1/patient-complaints', $payload)
            ->assertCreated()
            ->assertJsonPath('status', PatientComplaint::STATUS_BARU)
            ->assertJsonPath('category', 'pelayanan');

        $this->assertDatabaseHas('patient_complaints', [
            'patient_id' => $payload['patient_id'],
            'category' => 'pelayanan',
            'status' => PatientComplaint::STATUS_BARU,
            'handled_by' => null,
        ]);
    }

    public function test_daftar_komplain_tampil_untuk_pengguna_terautentikasi(): void
    {
        $this->actingUser();
        PatientComplaint::factory()->count(3)->create();

        $this->getJson('/api/v1/patient-complaints')
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_tamu_tidak_bisa_mengakses_daftar(): void
    {
        $this->getJson('/api/v1/patient-complaints')->assertUnauthorized();
    }

    public function test_lompatan_status_baru_ke_selesai_ditolak(): void
    {
        $this->actingUser();
        // Prasyarat "selesai" sengaja dilengkapi (resolution_notes ada) supaya
        // yang tertolak murni karena LOMPAT TAHAP, bukan karena catatan kosong.
        $komplain = PatientComplaint::factory()->create([
            'resolution_notes' => 'Catatan sudah siap.',
        ]);

        $this->patchJson("/api/v1/patient-complaints/{$komplain->id}", [
            'status' => PatientComplaint::STATUS_SELESAI,
        ])->assertStatus(422);

        $this->assertSame(PatientComplaint::STATUS_BARU, $komplain->refresh()->status);
    }

    public function test_naik_ke_diproses_wajib_punya_penanggung_jawab(): void
    {
        $this->actingUser();
        $komplain = PatientComplaint::factory()->create();

        $this->patchJson("/api/v1/patient-complaints/{$komplain->id}", [
            'status' => PatientComplaint::STATUS_DIPROSES,
        ])->assertStatus(422);

        // Setelah ditugaskan ke petugas, transisi sama lolos.
        $petugas = Employee::factory()->create();
        $this->patchJson("/api/v1/patient-complaints/{$komplain->id}", [
            'status' => PatientComplaint::STATUS_DIPROSES,
            'handled_by' => $petugas->id,
        ])->assertOk()->assertJsonPath('status', PatientComplaint::STATUS_DIPROSES);

        $this->assertSame($petugas->id, $komplain->refresh()->handled_by);
    }

    public function test_naik_ke_selesai_wajib_punya_catatan_penyelesaian(): void
    {
        $this->actingUser();
        $petugas = Employee::factory()->create();
        $komplain = PatientComplaint::factory()->create([
            'status' => PatientComplaint::STATUS_DIPROSES,
            'handled_by' => $petugas->id,
        ]);

        $this->patchJson("/api/v1/patient-complaints/{$komplain->id}", [
            'status' => PatientComplaint::STATUS_SELESAI,
        ])->assertStatus(422);

        $this->patchJson("/api/v1/patient-complaints/{$komplain->id}", [
            'status' => PatientComplaint::STATUS_SELESAI,
            'resolution_notes' => 'Sudah ditemui pasien dan diberi penjelasan.',
        ])->assertOk()->assertJsonPath('status', PatientComplaint::STATUS_SELESAI);
    }

    public function test_status_tidak_boleh_mundur(): void
    {
        $this->actingUser();
        $petugas = Employee::factory()->create();
        $komplain = PatientComplaint::factory()->create([
            'status' => PatientComplaint::STATUS_DIPROSES,
            'handled_by' => $petugas->id,
        ]);

        $this->patchJson("/api/v1/patient-complaints/{$komplain->id}", [
            'status' => PatientComplaint::STATUS_BARU,
        ])->assertStatus(422);
    }

    public function test_summary_menghitung_jumlah_per_status_dengan_angka_tepat(): void
    {
        $this->actingUser();
        $petugas = Employee::factory()->create();

        // 2 baru (default factory), 1 diproses, 1 selesai = 4 total.
        PatientComplaint::factory()->count(2)->create();
        PatientComplaint::factory()->create([
            'status' => PatientComplaint::STATUS_DIPROSES,
            'handled_by' => $petugas->id,
        ]);
        PatientComplaint::factory()->create([
            'status' => PatientComplaint::STATUS_SELESAI,
            'handled_by' => $petugas->id,
            'resolution_notes' => 'Selesai.',
        ]);

        $this->getJson('/api/v1/patient-complaints/summary')
            ->assertOk()
            ->assertJsonPath('data.baru', 2)
            ->assertJsonPath('data.diproses', 1)
            ->assertJsonPath('data.selesai', 1);

        // Filter satu status hanya mengembalikan key status itu.
        $this->getJson('/api/v1/patient-complaints/summary?status=diproses')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.diproses', 1);

        // Status typo ditolak, bukan diam-diam dianggap kosong.
        $this->getJson('/api/v1/patient-complaints/summary?status=proses')->assertStatus(422);
    }

    public function test_komplain_selesai_tidak_bisa_dihapus(): void
    {
        $this->actingUser();

        $selesai = PatientComplaint::factory()->create([
            'status' => PatientComplaint::STATUS_SELESAI,
            'resolution_notes' => 'Rekam mutu - tidak boleh hilang.',
        ]);
        $baru = PatientComplaint::factory()->create();

        $this->deleteJson("/api/v1/patient-complaints/{$selesai->id}")->assertStatus(422);
        $this->deleteJson("/api/v1/patient-complaints/{$baru->id}")->assertNoContent();

        $this->assertDatabaseHas('patient_complaints', ['id' => $selesai->id]);
        $this->assertDatabaseMissing('patient_complaints', ['id' => $baru->id]);
    }
}
