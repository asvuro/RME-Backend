<?php

namespace Modules\GeneralPrescriptionType\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralPrescriptionType\Models\PrescriptionType;

class PrescriptionTypeFactory extends Factory
{
    protected $model = PrescriptionType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}