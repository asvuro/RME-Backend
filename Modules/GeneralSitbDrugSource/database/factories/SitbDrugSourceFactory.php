<?php

namespace Modules\GeneralSitbDrugSource\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralSitbDrugSource\Models\SitbDrugSource;

class SitbDrugSourceFactory extends Factory
{
    protected $model = SitbDrugSource::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}