<?php

namespace Modules\GeneralSitbArt\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralSitbArt\Models\SitbArt;

class SitbArtFactory extends Factory
{
    protected $model = SitbArt::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}