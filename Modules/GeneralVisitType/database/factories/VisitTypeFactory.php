<?php

namespace Modules\GeneralVisitType\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralVisitType\Models\VisitType;

class VisitTypeFactory extends Factory
{
    protected $model = VisitType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}