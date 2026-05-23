<?php

namespace Modules\GeneralSitbThoraxNotDone\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralSitbThoraxNotDone\Models\SitbThoraxNotDone;

class SitbThoraxNotDoneFactory extends Factory
{
    protected $model = SitbThoraxNotDone::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}