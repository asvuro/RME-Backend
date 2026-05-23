<?php

namespace Modules\GeneralPayrollAddition\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralPayrollAddition\Models\PayrollAddition;

class PayrollAdditionFactory extends Factory
{
    protected $model = PayrollAddition::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}