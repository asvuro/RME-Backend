<?php

namespace Modules\GeneralHealthcareServiceType\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralHealthcareServiceType\Models\HealthcareServiceType;

class HealthcareServiceTypeFactory extends Factory
{
    protected $model = HealthcareServiceType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}