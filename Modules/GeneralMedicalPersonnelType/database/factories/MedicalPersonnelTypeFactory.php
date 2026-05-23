<?php

namespace Modules\GeneralMedicalPersonnelType\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralMedicalPersonnelType\Models\MedicalPersonnelType;

class MedicalPersonnelTypeFactory extends Factory
{
    protected $model = MedicalPersonnelType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}