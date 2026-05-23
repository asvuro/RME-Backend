<?php

namespace Modules\GeneralPainScaleMethod\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralPainScaleMethod\Models\PainScaleMethod;

class PainScaleMethodFactory extends Factory
{
    protected $model = PainScaleMethod::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}