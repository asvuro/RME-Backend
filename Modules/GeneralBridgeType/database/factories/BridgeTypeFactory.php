<?php

namespace Modules\GeneralBridgeType\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralBridgeType\Models\BridgeType;

class BridgeTypeFactory extends Factory
{
    protected $model = BridgeType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}