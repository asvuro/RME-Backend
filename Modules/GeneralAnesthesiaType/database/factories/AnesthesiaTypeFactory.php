<?php

namespace Modules\GeneralAnesthesiaType\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralAnesthesiaType\Models\AnesthesiaType;

class AnesthesiaTypeFactory extends Factory
{
    protected $model = AnesthesiaType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}