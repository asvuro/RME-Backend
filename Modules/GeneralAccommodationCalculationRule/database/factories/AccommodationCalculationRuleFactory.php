<?php

namespace Modules\GeneralAccommodationCalculationRule\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralAccommodationCalculationRule\Models\AccommodationCalculationRule;

class AccommodationCalculationRuleFactory extends Factory
{
    protected $model = AccommodationCalculationRule::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}