<?php

namespace Modules\LayananMortuaryRecord\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralEmployee\Models\Employee;
use Modules\GeneralPatient\Models\Patient;
use Modules\LayananMortuaryRecord\Models\MortuaryRecord;
use Modules\PendaftaranVisit\Models\Visit;
use Tests\TestCase;

/**
 * Kamar jenazah (#2): CRUD standar + gerbang rilis jenazah. Transisi status
 * in_mortuary -> released hanya boleh lewat endpoint release(), dan selalu
 * meninggalkan released_at + released_to + released_by yang lengkap.
 */
class MortuaryRecordControllerTest extends TestCase
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

    public function test_it_lists_mortuary_records(): void
    {
        $this->actingUser();
        MortuaryRecord::factory()->count(3)->create();

        $this->getJson('/api/v1/mortuary-records')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_petugas_can_admit_body_to_mortuary(): void
    {
        $this->actingUser();

        $payload = [
            // visit_id sengaja ditinggal null: jenazah DOA tidak lewat rawat inap.
            'patient_id' => Patient::factory()->create()->id,
            'admitted_at' => '2026-08-26 09:00:00',
            'cause_of_death_notes' => 'Henti jantung di IGD.',
            // Percobaan injeksi status harus gugur: validated() tidak membawa
            // field di luar rules(), dan status awal ditetapkan service.
            'status' => 'released',
        ];

        $response = $this->postJson('/api/v1/mortuary-records', $payload);

        $response->assertCreated();
        $record = MortuaryRecord::query()->sole();
        $this->assertSame(MortuaryRecord::STATUS_IN_MORTUARY, $record->status);
        $this->assertNull($record->visit_id);
        $this->assertNull($record->released_at);
    }

    public function test_admission_accepts_inpatient_visit_reference(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/mortuary-records', [
            'visit_id' => Visit::factory()->create()->id,
            'patient_id' => Patient::factory()->create()->id,
            'admitted_at' => '2026-08-26 10:30:00',
        ])->assertCreated();

        $this->assertSame(1, MortuaryRecord::count());
    }

    public function test_release_fills_release_data_and_switches_status(): void
    {
        $this->actingUser();
        $employee = Employee::factory()->create();
        $record = MortuaryRecord::factory()->create([
            'status' => MortuaryRecord::STATUS_IN_MORTUARY,
            'released_at' => null,
            'released_by' => null,
        ]);

        $response = $this->postJson("/api/v1/mortuary-records/{$record->id}/release", [
            'released_at' => '2026-08-26 15:00:00',
            'released_to_name' => 'Budi Santoso',
            'released_to_relationship' => 'Anak Kandung',
            'released_by' => $employee->id,
        ]);

        $response->assertOk()->assertJsonPath('data.status', MortuaryRecord::STATUS_RELEASED);

        // Jejak rilis wajib lengkap — inti gerbang #2.
        $this->assertDatabaseHas('mortuary_records', [
            'id' => $record->id,
            'status' => MortuaryRecord::STATUS_RELEASED,
            'released_at' => '2026-08-26 15:00:00',
            'released_to_name' => 'Budi Santoso',
            'released_by' => $employee->id,
        ]);
    }

    public function test_release_rejected_when_record_not_in_mortuary(): void
    {
        $this->actingUser();
        $record = MortuaryRecord::factory()->released()->create();

        $this->postJson("/api/v1/mortuary-records/{$record->id}/release", [
            'released_at' => '2026-08-27 08:00:00',
            'released_to_name' => 'Budi Santoso',
            'released_by' => Employee::factory()->create()->id,
        ])->assertStatus(422);

        // Data rilis pertama tidak boleh tertimpa rilis kedua.
        $this->assertNotSame(
            '2026-08-27 08:00:00',
            $record->refresh()->released_at?->format('Y-m-d H:i:s'),
        );
    }

    public function test_update_cannot_change_status_directly(): void
    {
        $this->actingUser();
        $record = MortuaryRecord::factory()->create();

        // Update umum hanya untuk detail non-status; satu-satunya transisi
        // status ada di endpoint release().
        $this->putJson("/api/v1/mortuary-records/{$record->id}", [
            'cause_of_death_notes' => 'Diperbarui hasil visum.',
            'status' => MortuaryRecord::STATUS_RELEASED,
        ])->assertOk();

        $record->refresh();
        $this->assertSame(MortuaryRecord::STATUS_IN_MORTUARY, $record->status);
        $this->assertSame('Diperbarui hasil visum.', $record->cause_of_death_notes);
    }

    public function test_delete_rejected_after_release(): void
    {
        $this->actingUser();
        $record = MortuaryRecord::factory()->released()->create();

        $this->deleteJson("/api/v1/mortuary-records/{$record->id}")->assertStatus(422);
        $this->assertSame(1, MortuaryRecord::count());
    }
}
