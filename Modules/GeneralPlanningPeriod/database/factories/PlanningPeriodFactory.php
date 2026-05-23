<?php

namespace Modules\GeneralPlanningPeriod\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralPlanningPeriod\Models\PlanningPeriod;

class PlanningPeriodFactory extends Factory
{
    protected $model = PlanningPeriod::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}