<?php

namespace Modules\GeneralSitbPreCulture\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralSitbPreCulture\Models\SitbPreCulture;

class SitbPreCultureFactory extends Factory
{
    protected $model = SitbPreCulture::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}