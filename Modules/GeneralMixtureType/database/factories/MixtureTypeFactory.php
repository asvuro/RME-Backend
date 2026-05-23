<?php

namespace Modules\GeneralMixtureType\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralMixtureType\Models\MixtureType;

class MixtureTypeFactory extends Factory
{
    protected $model = MixtureType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}