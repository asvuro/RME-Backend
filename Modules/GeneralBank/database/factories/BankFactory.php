<?php

namespace Modules\GeneralBank\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralBank\Models\Bank;

class BankFactory extends Factory
{
    protected $model = Bank::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}