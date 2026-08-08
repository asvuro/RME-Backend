<?php

namespace Modules\PendaftaranSelfCheckin\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralPatient\Models\Patient;
use Modules\GeneralWard\Models\Ward;
use Tests\TestCase;

class SelfCheckinStoreTest extends TestCase
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

    public function test_kiosk_can_check_in_patient_and_gets_queue_number_001(): void
    {
        $this->actingUser();
        $ward = Ward::factory()->create();
        $patient = Patient::factory()->create();

        $response = $this->postJson('/api/v1/self-checkin-queues', [
            'patient_id' => $patient->id,
            'ward_id' => $ward->id,
        ]);

        // Catatan: di produksi POST ini dipanggil kiosk dengan token service
        // account; token petugas lolos juga karena auth sengaja longgar.
        $response->assertCreated();
        $response->assertJsonPath('data.status', 'waiting');
        $response->assertJsonPath('data.queue_number', '001');
        $response->assertJsonPath('data.ward_id', $ward->id);
    }

    public function test_check_in_with_nik_only_is_accepted_for_new_patient(): void
    {
        $this->actingUser();

        $response = $this->postJson('/api/v1/self-checkin-queues', [
            'nik' => '3204010101900001',
        ]);

        $response->assertCreated();
        $response->assertJsonPath('data.nik', '****0001');
        $this->assertDatabaseHas('self_checkin_queues', [
            'patient_id' => null,
            'nik' => '3204010101900001',
            'status' => 'waiting',
        ]);
    }

    public function test_queue_numbers_increment_per_day_per_ward(): void
    {
        $this->actingUser();
        $ward = Ward::factory()->create();
        $patientA = Patient::factory()->create();
        $patientB = Patient::factory()->create();

        $this->postJson('/api/v1/self-checkin-queues', [
            'patient_id' => $patientA->id,
            'ward_id' => $ward->id,
        ])->assertCreated();

        // Pasien lain di poli yang sama harus dapat nomor berikutnya.
        $second = $this->postJson('/api/v1/self-checkin-queues', [
            'patient_id' => $patientB->id,
            'ward_id' => $ward->id,
        ]);

        $second->assertCreated();
        $second->assertJsonPath('data.queue_number', '002');
    }

    public function test_guest_cannot_check_in(): void
    {
        $this->postJson('/api/v1/self-checkin-queues', [
            'nik' => '3204010101900002',
        ])->assertStatus(401);
    }
}
