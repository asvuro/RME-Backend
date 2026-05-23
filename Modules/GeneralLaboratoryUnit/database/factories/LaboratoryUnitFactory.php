<?php

namespace Modules\GeneralLaboratoryUnit\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralLaboratoryUnit\Models\LaboratoryUnit;

class LaboratoryUnitFactory extends Factory
{
    protected $model = LaboratoryUnit::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}