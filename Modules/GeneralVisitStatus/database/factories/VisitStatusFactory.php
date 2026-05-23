<?php

namespace Modules\GeneralVisitStatus\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralVisitStatus\Models\VisitStatus;

class VisitStatusFactory extends Factory
{
    protected $model = VisitStatus::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}