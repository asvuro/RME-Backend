<?php

namespace Modules\GeneralIcdType\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralIcdType\Models\IcdType;

class IcdTypeFactory extends Factory
{
    protected $model = IcdType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}