<?php

namespace Modules\GeneralAgeGroup\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralAgeGroup\Models\AgeGroup;

class AgeGroupFactory extends Factory
{
    protected $model = AgeGroup::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}