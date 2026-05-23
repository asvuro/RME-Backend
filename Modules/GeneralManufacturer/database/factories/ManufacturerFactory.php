<?php

namespace Modules\GeneralManufacturer\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralManufacturer\Models\Manufacturer;

class ManufacturerFactory extends Factory
{
    protected $model = Manufacturer::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}