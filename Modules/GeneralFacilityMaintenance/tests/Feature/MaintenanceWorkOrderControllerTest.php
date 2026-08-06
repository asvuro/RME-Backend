<?php

namespace Modules\GeneralFacilityMaintenance\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralEmployee\Models\Employee;
use Modules\GeneralFacilityMaintenance\Models\MaintenanceAsset;
use Modules\GeneralFacilityMaintenance\Models\MaintenanceWorkOrder;
use Tests\TestCase;

class MaintenanceWorkOrderControllerTest extends TestCase
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

    public function test_it_creates_work_order_and_moves_asset_to_under_repair(): void
    {
        $this->actingUser();
        $asset = MaintenanceAsset::factory()->create(['status' => MaintenanceAsset::STATUS_OPERATIONAL]);
        $reporter = Employee::factory()->create();

        $this->postJson('/api/v1/work-orders', [
            'asset_id' => $asset->id,
            'reported_by' => $reporter->id,
            'issue_description' => 'AC bocor',
            'priority' => 'high',
        ])
            ->assertCreated()
            ->assertJsonPath('status', 'open');

        $this->assertSame(MaintenanceAsset::STATUS_UNDER_REPAIR, $asset->refresh()->status);
    }

    public function test_it_lists_work_orders(): void
    {
        $this->actingUser();
        MaintenanceWorkOrder::factory()->count(2)->create();

        $this->getJson('/api/v1/work-orders')
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_assign_endpoint_moves_work_order_to_in_progress(): void
    {
        $this->actingUser();
        $workOrder = MaintenanceWorkOrder::factory()->create(['status' => MaintenanceWorkOrder::STATUS_OPEN]);
        $employee = Employee::factory()->create();

        $this->postJson("/api/v1/work-orders/{$workOrder->id}/assign", ['assigned_to' => $employee->id])
            ->assertOk()
            ->assertJsonPath('status', 'in_progress')
            ->assertJsonPath('assigned_to', $employee->id);
    }

    public function test_complete_endpoint_rejected_when_work_order_not_in_progress(): void
    {
        // Gerbang utama modul ini: complete() hanya boleh dari in_progress.
        $this->actingUser();
        $workOrder = MaintenanceWorkOrder::factory()->create(['status' => MaintenanceWorkOrder::STATUS_OPEN]);

        $this->postJson("/api/v1/work-orders/{$workOrder->id}/complete")
            ->assertStatus(422);

        $this->assertSame(MaintenanceWorkOrder::STATUS_OPEN, $workOrder->refresh()->status);
    }

    public function test_complete_non_critical_returns_asset_to_operational(): void
    {
        $this->actingUser();
        $asset = MaintenanceAsset::factory()->create(['status' => MaintenanceAsset::STATUS_UNDER_REPAIR]);
        $workOrder = MaintenanceWorkOrder::factory()->create([
            'asset_id' => $asset->id,
            'status' => MaintenanceWorkOrder::STATUS_IN_PROGRESS,
            'priority' => MaintenanceWorkOrder::PRIORITY_HIGH,
        ]);

        $this->postJson("/api/v1/work-orders/{$workOrder->id}/complete")
            ->assertOk()
            ->assertJsonPath('status', 'completed')
            ->assertJsonPath('requires_manual_verification', false);

        $this->assertSame(MaintenanceAsset::STATUS_OPERATIONAL, $asset->refresh()->status);
    }

    public function test_complete_critical_priority_keeps_asset_out_of_service_pending_manual_verification(): void
    {
        // Gerbang khusus: priority critical tidak otomatis mengembalikan
        // asset ke operational, requires_manual_verification harus true.
        $this->actingUser();
        $asset = MaintenanceAsset::factory()->create(['status' => MaintenanceAsset::STATUS_UNDER_REPAIR]);
        $workOrder = MaintenanceWorkOrder::factory()->create([
            'asset_id' => $asset->id,
            'status' => MaintenanceWorkOrder::STATUS_IN_PROGRESS,
            'priority' => MaintenanceWorkOrder::PRIORITY_CRITICAL,
        ]);

        $this->postJson("/api/v1/work-orders/{$workOrder->id}/complete")
            ->assertOk()
            ->assertJsonPath('status', 'completed')
            ->assertJsonPath('requires_manual_verification', true);

        $this->assertSame(MaintenanceAsset::STATUS_UNDER_REPAIR, $asset->refresh()->status);
    }

    public function test_it_rejects_work_order_creation_for_decommissioned_asset(): void
    {
        $this->actingUser();
        $asset = MaintenanceAsset::factory()->create(['status' => MaintenanceAsset::STATUS_DECOMMISSIONED]);
        $reporter = Employee::factory()->create();

        $this->postJson('/api/v1/work-orders', [
            'asset_id' => $asset->id,
            'reported_by' => $reporter->id,
            'issue_description' => 'Cek ulang',
        ])->assertStatus(422);
    }
}
