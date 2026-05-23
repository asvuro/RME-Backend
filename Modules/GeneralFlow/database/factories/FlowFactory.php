<?php

namespace Modules\GeneralFlow\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralFlow\Models\Flow;

class FlowFactory extends Factory
{
    protected $model = Flow::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}