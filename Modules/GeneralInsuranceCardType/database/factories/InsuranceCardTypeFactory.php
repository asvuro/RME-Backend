<?php

namespace Modules\GeneralInsuranceCardType\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralInsuranceCardType\Models\InsuranceCardType;

class InsuranceCardTypeFactory extends Factory
{
    protected $model = InsuranceCardType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}