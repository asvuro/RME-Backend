<?php

namespace Modules\GeneralPatientStatus\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralPatientStatus\Models\PatientStatus;

class PatientStatusFactory extends Factory
{
    protected $model = PatientStatus::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}