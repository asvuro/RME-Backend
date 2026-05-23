<?php

namespace Modules\GeneralSitbEndMicroscopy\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralSitbEndMicroscopy\Models\SitbEndMicroscopy;

class SitbEndMicroscopyFactory extends Factory
{
    protected $model = SitbEndMicroscopy::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}