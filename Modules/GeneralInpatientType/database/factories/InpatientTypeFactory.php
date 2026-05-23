<?php

namespace Modules\GeneralInpatientType\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralInpatientType\Models\InpatientType;

class InpatientTypeFactory extends Factory
{
    protected $model = InpatientType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}