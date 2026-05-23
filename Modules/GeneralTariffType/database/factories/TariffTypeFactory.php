<?php

namespace Modules\GeneralTariffType\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralTariffType\Models\TariffType;

class TariffTypeFactory extends Factory
{
    protected $model = TariffType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}