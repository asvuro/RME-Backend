<?php

namespace Modules\GeneralSitbHivTestResult\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralSitbHivTestResult\Models\SitbHivTestResult;

class SitbHivTestResultFactory extends Factory
{
    protected $model = SitbHivTestResult::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}