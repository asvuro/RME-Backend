<?php

namespace Modules\GeneralPharmacyStatusType\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralPharmacyStatusType\Models\PharmacyStatusType;

class PharmacyStatusTypeFactory extends Factory
{
    protected $model = PharmacyStatusType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}