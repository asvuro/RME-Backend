<?php

namespace Modules\GeneralSitbMonth5Microscopy\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralSitbMonth5Microscopy\Models\SitbMonth5Microscopy;

class SitbMonth5MicroscopyFactory extends Factory
{
    protected $model = SitbMonth5Microscopy::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}