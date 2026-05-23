<?php

namespace Modules\GeneralDosageInstruction\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralDosageInstruction\Models\DosageInstruction;

class DosageInstructionFactory extends Factory
{
    protected $model = DosageInstruction::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}