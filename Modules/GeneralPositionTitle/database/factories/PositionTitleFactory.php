<?php

namespace Modules\GeneralPositionTitle\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralPositionTitle\Models\PositionTitle;

class PositionTitleFactory extends Factory
{
    protected $model = PositionTitle::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}