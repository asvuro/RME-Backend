<?php

namespace Modules\GeneralFacilityMaintenance\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralEmployee\Models\Employee;
use Modules\GeneralFacilityMaintenance\Models\MaintenanceAsset;
use Modules\GeneralFacilityMaintenance\Models\MaintenanceWorkOrder;

class MaintenanceWorkOrderFactory extends Factory
{
    protected $model = MaintenanceWorkOrder::class;

    public function definition(): array
    {
        return [
            'asset_id' => MaintenanceAsset::factory(),
            'reported_by' => Employee::factory(),
            'issue_description' => fake()->sentence(),
            'priority' => MaintenanceWorkOrder::PRIORITY_MEDIUM,
            'status' => MaintenanceWorkOrder::STATUS_OPEN,
            'assigned_to' => null,
            'reported_at' => now(),
            'completed_at' => null,
            'requires_manual_verification' => false,
        ];
    }
}
