<?php

namespace Modules\GeneralMonthName\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralMonthName\Models\MonthName;

class MonthNameFactory extends Factory
{
    protected $model = MonthName::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}