<?php

namespace Modules\GeneralSitbMonth2Microscopy\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralSitbMonth2Microscopy\Models\SitbMonth2Microscopy;

class SitbMonth2MicroscopyFactory extends Factory
{
    protected $model = SitbMonth2Microscopy::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}