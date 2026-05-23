<?php

namespace Modules\GeneralReturnCancellationType\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralReturnCancellationType\Models\ReturnCancellationType;

class ReturnCancellationTypeFactory extends Factory
{
    protected $model = ReturnCancellationType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}