<?php

namespace Modules\GeneralSitbReferrerType\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralSitbReferrerType\Models\SitbReferrerType;

class SitbReferrerTypeFactory extends Factory
{
    protected $model = SitbReferrerType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}