<?php

namespace Modules\GeneralSitbMonth3Microscopy\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralSitbMonth3Microscopy\Models\SitbMonth3Microscopy;

class SitbMonth3MicroscopyFactory extends Factory
{
    protected $model = SitbMonth3Microscopy::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}