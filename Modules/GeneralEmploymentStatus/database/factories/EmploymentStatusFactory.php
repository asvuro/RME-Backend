<?php

namespace Modules\GeneralEmploymentStatus\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralEmploymentStatus\Models\EmploymentStatus;

class EmploymentStatusFactory extends Factory
{
    protected $model = EmploymentStatus::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}