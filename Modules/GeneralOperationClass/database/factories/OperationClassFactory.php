<?php

namespace Modules\GeneralOperationClass\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralOperationClass\Models\OperationClass;

class OperationClassFactory extends Factory
{
    protected $model = OperationClass::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}