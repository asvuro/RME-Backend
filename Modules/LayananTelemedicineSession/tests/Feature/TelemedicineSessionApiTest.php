<?php

namespace Modules\LayananTelemedicineSession\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralEmployee\Database\Factories\EmployeeFactory;
use Modules\LayananTelemedicineSession\Models\TelemedicineSession;
use Modules\PendaftaranVisit\Models\Visit;
use Tests\TestCase;

/**
 * API sesi telemedicine: CRUD + gerbang urutan status
 * scheduled -> ongoing -> completed (cabang cancelled).
 */
class TelemedicineSessionApiTest extends TestCase
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

    public function test_tamu_ditolak_401(): void
    {
        $this->getJson('/api/v1/telemedicine-sessions')->assertUnauthorized();
    }

    /** (a) create/store berhasil oleh petugas; status & placeholder url diisi service. */
    public function test_petugas_dapat_menjadwalkan_sesi_baru(): void
    {
        $this->actingUser();
        $visit = Visit::factory()->create();
        $doctor = EmployeeFactory::new()->create();

        $response = $this->postJson('/api/v1/telemedicine-sessions', [
            'visit_id' => $visit->id,
            'doctor_employee_id' => $doctor->id,
            'scheduled_at' => now()->addDay()->toIso8601String(),
        ]);

        $response->assertCreated();

        $id = $response->json('data.id');
        $this->assertDatabaseHas('telemedicine_sessions', [
            'id' => $id,
            'visit_id' => $visit->id,
            'doctor_employee_id' => $doctor->id,
            'status' => 'scheduled',
            'started_at' => null,
            'ended_at' => null,
        ]);

        // Placeholder ruang video call terisi otomatis (integrasi asli belum ada).
        $this->assertStringStartsWith('/telemedicine/rooms/', (string) $response->json('data.session_url'));
    }

    public function test_store_ditolak_bila_kunjungan_masih_punya_sesi_aktif(): void
    {
        $this->actingUser();
        $session = TelemedicineSession::factory()->create();

        $response = $this->postJson('/api/v1/telemedicine-sessions', [
            'visit_id' => $session->visit_id,
            'doctor_employee_id' => $session->doctor_employee_id,
            'scheduled_at' => now()->addDays(2)->toIso8601String(),
        ]);

        $response->assertStatus(422);
        $this->assertSame(1, TelemedicineSession::count());
    }

    /** (b) Gerbang urutan utama: complete sebelum start ditolak. */
    public function test_complete_sebelum_start_ditolak_422(): void
    {
        $this->actingUser();
        $session = TelemedicineSession::factory()->create();

        $this->postJson("/api/v1/telemedicine-sessions/{$session->id}/complete")
            ->assertStatus(422);

        // Tidak ada efek samping: masih scheduled, belum ada jam selesai.
        $this->assertSame('scheduled', $session->refresh()->status);
        $this->assertNull($session->ended_at);
    }

    public function test_start_ditolak_bila_kunjungan_sudah_pulang(): void
    {
        $this->actingUser();
        $visit = Visit::factory()->discharged()->create();
        $session = TelemedicineSession::factory()->create(['visit_id' => $visit->id]);

        $this->postJson("/api/v1/telemedicine-sessions/{$session->id}/start")
            ->assertStatus(422);

        $this->assertSame('scheduled', $session->refresh()->status);
        $this->assertNull($session->started_at);
    }

    public function test_urutan_lengkap_scheduled_ke_completed_lalu_terkunci(): void
    {
        $this->actingUser();
        $session = TelemedicineSession::factory()->create();

        $started = $this->postJson("/api/v1/telemedicine-sessions/{$session->id}/start");
        $started->assertOk();
        $this->assertSame('ongoing', $started->json('data.status'));
        $this->assertNotNull($started->json('data.started_at'));

        $completed = $this->postJson("/api/v1/telemedicine-sessions/{$session->id}/complete", [
            'consultation_notes' => 'Pasien stabil, kontrol ulang 2 minggu.',
        ]);
        $completed->assertOk();
        $this->assertSame('completed', $completed->json('data.status'));
        $this->assertNotNull($completed->json('data.ended_at'));
        $this->assertSame('Pasien stabil, kontrol ulang 2 minggu.', $completed->json('data.consultation_notes'));

        // Mesin status linear: start kedua setelah completed ditolak.
        $this->postJson("/api/v1/telemedicine-sessions/{$session->id}/start")->assertStatus(422);
    }

    public function test_update_sesi_sudah_completed_ditolak_422(): void
    {
        $this->actingUser();
        $session = TelemedicineSession::factory()->completed()->create();

        $this->putJson("/api/v1/telemedicine-sessions/{$session->id}", [
            'consultation_notes' => 'dicoba diubah setelah selesai',
        ])->assertStatus(422);

        // Pembatalan atas sesi completed juga ditolak.
        $this->putJson("/api/v1/telemedicine-sessions/{$session->id}", [
            'status' => 'cancelled',
        ])->assertStatus(422);
    }

    /** (c) index menampilkan daftar + filter per kunjungan. */
    public function test_index_menampilkan_daftar_sesi(): void
    {
        $this->actingUser();

        $visitA = Visit::factory()->create();
        $visitB = Visit::factory()->create();

        $a1 = TelemedicineSession::factory()->create(['visit_id' => $visitA->id]);
        $b1 = TelemedicineSession::factory()->create(['visit_id' => $visitB->id]);
        $b2 = TelemedicineSession::factory()->completed()->create(['visit_id' => $visitB->id]);

        $this->getJson('/api/v1/telemedicine-sessions')
            ->assertOk()
            ->assertJsonCount(3, 'data');

        $this->getJson("/api/v1/telemedicine-sessions?visit_id={$visitB->id}")
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.id', $b2->id)
            ->assertJsonPath('data.1.id', $b1->id);

        $this->assertNotNull($a1);
    }
}
