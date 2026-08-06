<?php

namespace Modules\GeneralFacilityMaintenance\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralFacilityMaintenance\Models\MaintenanceAsset;

class MaintenanceAssetFactory extends Factory
{
    protected $model = MaintenanceAsset::class;

    public function definition(): array
    {
        return [
            'asset_code' => fake()->unique()->bothify('AST-#####'),
            'asset_name' => fake()->words(3, true),
            'location' => fake()->streetName(),
            'ward_id' => null,
            'status' => MaintenanceAsset::STATUS_OPERATIONAL,
        ];
    }
}
