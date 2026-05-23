<?php

namespace Modules\GeneralReferralType\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralReferralType\Models\ReferralType;

class ReferralTypeFactory extends Factory
{
    protected $model = ReferralType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}