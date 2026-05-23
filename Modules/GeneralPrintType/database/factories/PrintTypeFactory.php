<?php

namespace Modules\GeneralPrintType\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralPrintType\Models\PrintType;

class PrintTypeFactory extends Factory
{
    protected $model = PrintType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}