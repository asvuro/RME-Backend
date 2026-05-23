<?php

namespace Modules\GeneralSitbDmTherapy\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralSitbDmTherapy\Models\SitbDmTherapy;

class SitbDmTherapyFactory extends Factory
{
    protected $model = SitbDmTherapy::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}