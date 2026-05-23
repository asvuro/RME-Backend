<?php

namespace Modules\GeneralIdentityCardType\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralIdentityCardType\Models\IdentityCardType;

class IdentityCardTypeFactory extends Factory
{
    protected $model = IdentityCardType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}