<?php

namespace Modules\GeneralSitbChestXrayResult\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralSitbChestXrayResult\Models\SitbChestXrayResult;

class SitbChestXrayResultFactory extends Factory
{
    protected $model = SitbChestXrayResult::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}