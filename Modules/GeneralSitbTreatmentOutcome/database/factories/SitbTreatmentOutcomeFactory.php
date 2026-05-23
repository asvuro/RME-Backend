<?php

namespace Modules\GeneralSitbTreatmentOutcome\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralSitbTreatmentOutcome\Models\SitbTreatmentOutcome;

class SitbTreatmentOutcomeFactory extends Factory
{
    protected $model = SitbTreatmentOutcome::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}