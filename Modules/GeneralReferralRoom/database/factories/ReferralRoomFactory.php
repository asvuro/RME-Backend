<?php

namespace Modules\GeneralReferralRoom\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralReferralRoom\Models\ReferralRoom;

class ReferralRoomFactory extends Factory
{
    protected $model = ReferralRoom::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}