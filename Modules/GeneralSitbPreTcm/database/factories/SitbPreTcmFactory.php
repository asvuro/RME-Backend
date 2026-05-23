<?php

namespace Modules\GeneralSitbPreTcm\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralSitbPreTcm\Models\SitbPreTcm;

class SitbPreTcmFactory extends Factory
{
    protected $model = SitbPreTcm::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}