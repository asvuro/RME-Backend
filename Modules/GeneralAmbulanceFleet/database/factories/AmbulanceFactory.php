<?php

namespace Modules\GeneralAmbulanceFleet\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralAmbulanceFleet\Models\Ambulance;

class AmbulanceFactory extends Factory
{
    protected $model = Ambulance::class;

    public function definition(): array
    {
        return [
            'vehicle_code' => 'AMB-'.fake()->unique()->numberBetween(1000, 9999),
            'plate_number' => 'B '.fake()->numberBetween(1000, 9999).' '.strtoupper(fake()->lexify('???')),
            // Armada baru selalu available; state lain muncul dari kejadian bisnis.
            'status' => Ambulance::STATUS_AVAILABLE,
        ];
    }

    /** State uji: ambulans sedang menjalankan trip. */
    public function inUse(): self
    {
        return $this->state(fn () => ['status' => Ambulance::STATUS_IN_USE]);
    }

    /** State uji: ambulans sedang servis/perbaikan. */
    public function maintenance(): self
    {
        return $this->state(fn () => ['status' => Ambulance::STATUS_MAINTENANCE]);
    }
}
