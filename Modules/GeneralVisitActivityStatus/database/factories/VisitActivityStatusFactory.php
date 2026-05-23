<?php

namespace Modules\GeneralVisitActivityStatus\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralVisitActivityStatus\Models\VisitActivityStatus;

class VisitActivityStatusFactory extends Factory
{
    protected $model = VisitActivityStatus::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}