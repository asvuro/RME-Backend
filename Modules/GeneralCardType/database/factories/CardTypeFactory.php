<?php

namespace Modules\GeneralCardType\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralCardType\Models\CardType;

class CardTypeFactory extends Factory
{
    protected $model = CardType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}