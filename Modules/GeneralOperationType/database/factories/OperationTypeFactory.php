<?php

namespace Modules\GeneralOperationType\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralOperationType\Models\OperationType;

class OperationTypeFactory extends Factory
{
    protected $model = OperationType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}