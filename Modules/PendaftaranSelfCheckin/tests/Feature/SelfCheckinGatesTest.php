<?php

namespace Modules\PendaftaranSelfCheckin\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\PendaftaranSelfCheckin\Models\SelfCheckinQueue;
use Tests\TestCase;

class SelfCheckinGatesTest extends TestCase
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

    public function test_complete_before_call_is_rejected(): void
    {
        $this->actingUser();
        // Gerbang urutan: antrian masih waiting, complete harus ditolak.
        $queue = SelfCheckinQueue::factory()->create(['status' => SelfCheckinQueue::STATUS_WAITING]);

        $response = $this->postJson("/api/v1/self-checkin-queues/{$queue->id}/complete");

        $response->assertStatus(422);
        $this->assertDatabaseHas('self_checkin_queues', [
            'id' => $queue->id,
            'status' => SelfCheckinQueue::STATUS_WAITING,
        ]);
    }

    public function test_call_then_complete_transitions_status_and_records_actor(): void
    {
        $user = $this->actingUser();
        $queue = SelfCheckinQueue::factory()->create();

        $call = $this->postJson("/api/v1/self-checkin-queues/{$queue->id}/call");

        $call->assertOk();
        $call->assertJsonPath('data.status', 'called');
        // Aktor call = petugas loket yang menekan tombol, bukan device kiosk.
        $call->assertJsonPath('data.called_by', $user->id);
        $this->assertNotNull($queue->fresh()->called_at);

        $complete = $this->postJson("/api/v1/self-checkin-queues/{$queue->id}/complete");

        $complete->assertOk();
        $complete->assertJsonPath('data.status', 'completed');
    }

    public function test_calling_twice_is_rejected(): void
    {
        $this->actingUser();
        $queue = SelfCheckinQueue::factory()->called()->create();

        $this->postJson("/api/v1/self-checkin-queues/{$queue->id}/call")->assertStatus(422);
    }

    public function test_same_patient_cannot_have_two_active_queues_in_same_ward_today(): void
    {
        $this->actingUser();
        $payload = ['nik' => '3204010101900003'];

        $this->postJson('/api/v1/self-checkin-queues', $payload)->assertCreated();

        // Gerbang anti-duplikat: NIK sama masih punya antrian aktif hari ini.
        $this->postJson('/api/v1/self-checkin-queues', $payload)->assertStatus(422);
    }

    public function test_check_in_without_any_identity_is_rejected(): void
    {
        $this->actingUser();

        // FormRequest menolak (required_without); gerbang identitas di service
        // adalah pertahanan kedua bila validasi dilewati.
        $this->postJson('/api/v1/self-checkin-queues', [])->assertStatus(422);
    }
}
