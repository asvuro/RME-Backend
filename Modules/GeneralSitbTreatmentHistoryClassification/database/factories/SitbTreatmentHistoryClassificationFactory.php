<?php

namespace Modules\GeneralSitbTreatmentHistoryClassification\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralSitbTreatmentHistoryClassification\Models\SitbTreatmentHistoryClassification;

class SitbTreatmentHistoryClassificationFactory extends Factory
{
    protected $model = SitbTreatmentHistoryClassification::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}