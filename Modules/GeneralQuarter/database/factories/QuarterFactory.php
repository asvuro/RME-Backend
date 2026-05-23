<?php

namespace Modules\GeneralQuarter\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralQuarter\Models\Quarter;

class QuarterFactory extends Factory
{
    protected $model = Quarter::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}