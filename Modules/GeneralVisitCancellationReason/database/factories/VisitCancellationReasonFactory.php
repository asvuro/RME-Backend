<?php

namespace Modules\GeneralVisitCancellationReason\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralVisitCancellationReason\Models\VisitCancellationReason;

class VisitCancellationReasonFactory extends Factory
{
    protected $model = VisitCancellationReason::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}