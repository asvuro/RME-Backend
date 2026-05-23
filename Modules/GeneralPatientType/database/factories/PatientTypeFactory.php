<?php

namespace Modules\GeneralPatientType\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralPatientType\Models\PatientType;

class PatientTypeFactory extends Factory
{
    protected $model = PatientType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}