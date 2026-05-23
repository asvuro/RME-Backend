<?php

namespace Modules\GeneralSitbHivStatusClassification\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralSitbHivStatusClassification\Models\SitbHivStatusClassification;

class SitbHivStatusClassificationFactory extends Factory
{
    protected $model = SitbHivStatusClassification::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}