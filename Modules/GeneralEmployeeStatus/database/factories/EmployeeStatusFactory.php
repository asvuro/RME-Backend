<?php

namespace Modules\GeneralEmployeeStatus\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralEmployeeStatus\Models\EmployeeStatus;

class EmployeeStatusFactory extends Factory
{
    protected $model = EmployeeStatus::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}