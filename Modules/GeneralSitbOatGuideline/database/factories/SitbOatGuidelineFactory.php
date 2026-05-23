<?php

namespace Modules\GeneralSitbOatGuideline\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralSitbOatGuideline\Models\SitbOatGuideline;

class SitbOatGuidelineFactory extends Factory
{
    protected $model = SitbOatGuideline::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}