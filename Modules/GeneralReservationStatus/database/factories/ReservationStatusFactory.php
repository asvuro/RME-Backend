<?php

namespace Modules\GeneralReservationStatus\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralReservationStatus\Models\ReservationStatus;

class ReservationStatusFactory extends Factory
{
    protected $model = ReservationStatus::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}