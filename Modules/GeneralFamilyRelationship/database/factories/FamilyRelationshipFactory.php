<?php

namespace Modules\GeneralFamilyRelationship\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralFamilyRelationship\Models\FamilyRelationship;

class FamilyRelationshipFactory extends Factory
{
    protected $model = FamilyRelationship::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}