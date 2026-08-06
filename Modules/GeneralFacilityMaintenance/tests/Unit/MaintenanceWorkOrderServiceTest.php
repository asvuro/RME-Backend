<?php

namespace Modules\GeneralFacilityMaintenance\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\GeneralFacilityMaintenance\Models\MaintenanceAsset;
use Modules\GeneralFacilityMaintenance\Models\MaintenanceWorkOrder;
use Modules\GeneralFacilityMaintenance\Services\MaintenanceWorkOrderService;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class MaintenanceWorkOrderServiceTest extends TestCase
{
    use RefreshDatabase;

    protected MaintenanceWorkOrderService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(MaintenanceWorkOrderService::class);
    }

    public function test_assign_ditolak_bila_work_order_sudah_completed(): void
    {
        $workOrder = MaintenanceWorkOrder::factory()->create(['status' => MaintenanceWorkOrder::STATUS_COMPLETED]);

        try {
            $this->service->assign($workOrder->id, $workOrder->reported_by);
            $this->fail('Harusnya ditolak.');
        } catch (HttpException $e) {
            $this->assertSame(422, $e->getStatusCode());
        }
    }

    public function test_complete_ditolak_bila_status_bukan_in_progress(): void
    {
        $workOrder = MaintenanceWorkOrder::factory()->create(['status' => MaintenanceWorkOrder::STATUS_OPEN]);

        $this->assertThrows(
            fn () => $this->service->complete($workOrder->id),
            HttpException::class,
        );

        $this->assertSame(MaintenanceWorkOrder::STATUS_OPEN, $workOrder->refresh()->status);
    }

    public function test_complete_priority_critical_menandai_requires_manual_verification(): void
    {
        $asset = MaintenanceAsset::factory()->create(['status' => MaintenanceAsset::STATUS_UNDER_REPAIR]);
        $workOrder = MaintenanceWorkOrder::factory()->create([
            'asset_id' => $asset->id,
            'status' => MaintenanceWorkOrder::STATUS_IN_PROGRESS,
            'priority' => MaintenanceWorkOrder::PRIORITY_CRITICAL,
        ]);

        $result = $this->service->complete($workOrder->id);

        $this->assertTrue($result->requires_manual_verification);
        $this->assertSame(MaintenanceAsset::STATUS_UNDER_REPAIR, $asset->refresh()->status);
    }

    public function test_create_work_order_ditolak_untuk_asset_decommissioned(): void
    {
        $asset = MaintenanceAsset::factory()->create(['status' => MaintenanceAsset::STATUS_DECOMMISSIONED]);

        $this->assertThrows(
            fn () => $this->service->createWorkOrder([
                'asset_id' => $asset->id,
                'reported_by' => \Modules\GeneralEmployee\Models\Employee::factory()->create()->id,
                'issue_description' => 'Tes',
            ]),
            HttpException::class,
        );
    }
}
