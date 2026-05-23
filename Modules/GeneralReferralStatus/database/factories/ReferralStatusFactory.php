<?php

namespace Modules\GeneralReferralStatus\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralReferralStatus\Models\ReferralStatus;

class ReferralStatusFactory extends Factory
{
    protected $model = ReferralStatus::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}