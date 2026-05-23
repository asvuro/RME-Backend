<?php

namespace Modules\GeneralBedStatus\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralBedStatus\Models\BedStatus;

class BedStatusFactory extends Factory
{
    protected $model = BedStatus::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}