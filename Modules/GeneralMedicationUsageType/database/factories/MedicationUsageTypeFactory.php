<?php

namespace Modules\GeneralMedicationUsageType\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralMedicationUsageType\Models\MedicationUsageType;

class MedicationUsageTypeFactory extends Factory
{
    protected $model = MedicationUsageType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}