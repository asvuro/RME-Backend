<?php

namespace Modules\GeneralPayrollDeduction\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralPayrollDeduction\Models\PayrollDeduction;

class PayrollDeductionFactory extends Factory
{
    protected $model = PayrollDeduction::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}