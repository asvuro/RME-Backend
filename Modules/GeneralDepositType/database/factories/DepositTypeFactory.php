<?php

namespace Modules\GeneralDepositType\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralDepositType\Models\DepositType;

class DepositTypeFactory extends Factory
{
    protected $model = DepositType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}