<?php

namespace Modules\GeneralHealthProviderType\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralHealthProviderType\Models\HealthProviderType;

class HealthProviderTypeFactory extends Factory
{
    protected $model = HealthProviderType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}