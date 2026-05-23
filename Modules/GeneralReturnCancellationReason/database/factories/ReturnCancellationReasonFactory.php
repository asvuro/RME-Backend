<?php

namespace Modules\GeneralReturnCancellationReason\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralReturnCancellationReason\Models\ReturnCancellationReason;

class ReturnCancellationReasonFactory extends Factory
{
    protected $model = ReturnCancellationReason::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}