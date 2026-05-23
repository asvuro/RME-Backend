<?php

namespace Modules\GeneralFacilityOwnershipType\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralFacilityOwnershipType\Models\FacilityOwnershipType;

class FacilityOwnershipTypeFactory extends Factory
{
    protected $model = FacilityOwnershipType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}