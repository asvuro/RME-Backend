<?php

namespace Modules\GeneralOperationGroup\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralOperationGroup\Models\OperationGroup;

class OperationGroupFactory extends Factory
{
    protected $model = OperationGroup::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}