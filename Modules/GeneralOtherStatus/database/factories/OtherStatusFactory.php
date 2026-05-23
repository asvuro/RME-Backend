<?php

namespace Modules\GeneralOtherStatus\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralOtherStatus\Models\OtherStatus;

class OtherStatusFactory extends Factory
{
    protected $model = OtherStatus::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}