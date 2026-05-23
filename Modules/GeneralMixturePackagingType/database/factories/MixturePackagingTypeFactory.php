<?php

namespace Modules\GeneralMixturePackagingType\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralMixturePackagingType\Models\MixturePackagingType;

class MixturePackagingTypeFactory extends Factory
{
    protected $model = MixturePackagingType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}