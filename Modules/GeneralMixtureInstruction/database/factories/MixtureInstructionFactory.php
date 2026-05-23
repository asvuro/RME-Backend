<?php

namespace Modules\GeneralMixtureInstruction\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralMixtureInstruction\Models\MixtureInstruction;

class MixtureInstructionFactory extends Factory
{
    protected $model = MixtureInstruction::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}