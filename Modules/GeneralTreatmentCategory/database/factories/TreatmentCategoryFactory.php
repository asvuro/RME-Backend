<?php

namespace Modules\GeneralTreatmentCategory\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralTreatmentCategory\Models\TreatmentCategory;

class TreatmentCategoryFactory extends Factory
{
    protected $model = TreatmentCategory::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}