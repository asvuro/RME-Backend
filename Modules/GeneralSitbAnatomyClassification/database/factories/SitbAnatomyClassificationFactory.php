<?php

namespace Modules\GeneralSitbAnatomyClassification\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralSitbAnatomyClassification\Models\SitbAnatomyClassification;

class SitbAnatomyClassificationFactory extends Factory
{
    protected $model = SitbAnatomyClassification::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}