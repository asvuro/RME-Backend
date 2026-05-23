<?php

namespace Modules\GeneralActiveIngredient\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralActiveIngredient\Models\ActiveIngredient;

class ActiveIngredientFactory extends Factory
{
    protected $model = ActiveIngredient::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}