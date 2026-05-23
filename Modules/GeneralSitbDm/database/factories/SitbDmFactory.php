<?php

namespace Modules\GeneralSitbDm\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralSitbDm\Models\SitbDm;

class SitbDmFactory extends Factory
{
    protected $model = SitbDm::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}