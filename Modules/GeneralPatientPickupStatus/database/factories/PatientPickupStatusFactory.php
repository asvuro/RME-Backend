<?php

namespace Modules\GeneralPatientPickupStatus\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralPatientPickupStatus\Models\PatientPickupStatus;

class PatientPickupStatusFactory extends Factory
{
    protected $model = PatientPickupStatus::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}