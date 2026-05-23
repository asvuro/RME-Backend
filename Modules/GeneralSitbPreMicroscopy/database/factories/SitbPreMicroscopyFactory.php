<?php

namespace Modules\GeneralSitbPreMicroscopy\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralSitbPreMicroscopy\Models\SitbPreMicroscopy;

class SitbPreMicroscopyFactory extends Factory
{
    protected $model = SitbPreMicroscopy::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}