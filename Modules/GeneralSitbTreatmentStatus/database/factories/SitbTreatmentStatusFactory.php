<?php

namespace Modules\GeneralSitbTreatmentStatus\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralSitbTreatmentStatus\Models\SitbTreatmentStatus;

class SitbTreatmentStatusFactory extends Factory
{
    protected $model = SitbTreatmentStatus::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}