<?php

namespace Modules\LayananLabAnalyzerOrder\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralEmployee\Models\Employee;
use Modules\LayananLabAnalyzerOrder\Models\LabAnalyzerOrder;
use Tests\TestCase;

class LabAnalyzerOrderVerifyGateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }

    /**
     * Verifikasi menulis ke verified_by (FK employees), jadi user login uji
     * harus punya profil pegawai - sama seperti kondisi nyata di layanan.
     */
    private function actingPetugasWithProfile(): User
    {
        $user = User::factory()->create();
        $user->assignRole('petugas');
        Employee::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user, 'sanctum');

        return $user;
    }

    public function test_verify_is_rejected_from_ordered_status(): void
    {
        $this->actingPetugasWithProfile();
        // Order masih 'ordered': melompat langsung ke verifikasi ditolak 422.
        $order = LabAnalyzerOrder::factory()->create();

        $this->postJson("/api/v1/lab-analyzer-orders/{$order->id}/verify")
            ->assertStatus(422);

        $this->assertDatabaseHas('lab_analyzer_orders', [
            'id' => $order->id,
            'status' => 'ordered',
            'verified_by' => null,
            'verified_at' => null,
        ]);
    }

    public function test_verify_is_rejected_from_sent_to_analyzer_status(): void
    {
        $this->actingPetugasWithProfile();
        $order = LabAnalyzerOrder::factory()->create([
            'status' => LabAnalyzerOrder::STATUS_SENT_TO_ANALYZER,
        ]);

        $this->postJson("/api/v1/lab-analyzer-orders/{$order->id}/verify")
            ->assertStatus(422);

        $this->assertDatabaseHas('lab_analyzer_orders', [
            'id' => $order->id,
            'status' => 'sent_to_analyzer',
            'verified_by' => null,
        ]);
    }

    public function test_verify_from_result_received_stamps_employee_verifier(): void
    {
        $user = $this->actingPetugasWithProfile();
        $order = LabAnalyzerOrder::factory()->resultReceived()->create();

        $this->postJson("/api/v1/lab-analyzer-orders/{$order->id}/verify")
            ->assertOk()
            ->assertJsonPath('data.status', 'verified');

        $employeeId = Employee::query()->where('user_id', $user->id)->value('id');

        $this->assertDatabaseHas('lab_analyzer_orders', [
            'id' => $order->id,
            'status' => 'verified',
            'verified_by' => $employeeId,
        ]);
        $this->assertNotNull($order->fresh()->verified_at);
    }

    public function test_verify_is_rejected_when_user_has_no_employee_profile(): void
    {
        $user = User::factory()->create();
        $user->assignRole('petugas');
        $this->actingAs($user, 'sanctum');

        // Sengaja TANPA profil pegawai: verified_by tak bisa diisi valid.
        $order = LabAnalyzerOrder::factory()->resultReceived()->create();

        $this->postJson("/api/v1/lab-analyzer-orders/{$order->id}/verify")
            ->assertStatus(422);

        $this->assertDatabaseHas('lab_analyzer_orders', [
            'id' => $order->id,
            'status' => 'result_received',
            'verified_by' => null,
        ]);
    }

    public function test_full_state_machine_flow_via_api(): void
    {
        $this->actingPetugasWithProfile();
        $order = LabAnalyzerOrder::factory()->create();

        // ordered -> sent_to_analyzer
        $this->postJson("/api/v1/lab-analyzer-orders/{$order->id}/send-to-analyzer")
            ->assertOk()
            ->assertJsonPath('data.status', 'sent_to_analyzer');

        // sent_to_analyzer -> result_received (hasil mentah apa adanya)
        $this->postJson("/api/v1/lab-analyzer-orders/{$order->id}/result", [
            'raw_result_text' => '<OBX>|||HBA1C|5.9|%</OBX>',
        ])->assertOk()
            ->assertJsonPath('data.status', 'result_received')
            ->assertJsonPath('data.raw_result_text', '<OBX>|||HBA1C|5.9|%</OBX>');

        // result_received -> verified
        $this->postJson("/api/v1/lab-analyzer-orders/{$order->id}/verify")
            ->assertOk()
            ->assertJsonPath('data.status', 'verified');
    }
}
