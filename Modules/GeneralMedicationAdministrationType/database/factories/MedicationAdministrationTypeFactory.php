<?php

namespace Modules\GeneralMedicationAdministrationType\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralMedicationAdministrationType\Models\MedicationAdministrationType;

class MedicationAdministrationTypeFactory extends Factory
{
    protected $model = MedicationAdministrationType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}