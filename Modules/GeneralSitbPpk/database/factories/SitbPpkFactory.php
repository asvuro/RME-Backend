<?php

namespace Modules\GeneralSitbPpk\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralSitbPpk\Models\SitbPpk;

class SitbPpkFactory extends Factory
{
    protected $model = SitbPpk::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}