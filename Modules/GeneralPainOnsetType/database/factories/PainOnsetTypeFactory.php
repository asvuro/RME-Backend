<?php

namespace Modules\GeneralPainOnsetType\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralPainOnsetType\Models\PainOnsetType;

class PainOnsetTypeFactory extends Factory
{
    protected $model = PainOnsetType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}