<?php

namespace Modules\GeneralTbPatientCategory\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralTbPatientCategory\Models\TbPatientCategory;

class TbPatientCategoryFactory extends Factory
{
    protected $model = TbPatientCategory::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}