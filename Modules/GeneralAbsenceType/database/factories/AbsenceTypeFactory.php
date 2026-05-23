<?php

namespace Modules\GeneralAbsenceType\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralAbsenceType\Models\AbsenceType;

class AbsenceTypeFactory extends Factory
{
    protected $model = AbsenceType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}