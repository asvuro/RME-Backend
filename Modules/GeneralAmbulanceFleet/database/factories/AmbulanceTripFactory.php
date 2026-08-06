<?php

namespace Modules\GeneralAmbulanceFleet\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralAmbulanceFleet\Models\Ambulance;
use Modules\GeneralAmbulanceFleet\Models\AmbulanceTrip;
use Modules\GeneralEmployee\Models\Employee;

class AmbulanceTripFactory extends Factory
{
    protected $model = AmbulanceTrip::class;

    public function definition(): array
    {
        return [
            'ambulance_id' => Ambulance::factory(),
            // Nullable: trip jemput/antar jenazah belum tentu terkait pasien terdaftar.
            'patient_id' => null,
            'driver_employee_id' => Employee::factory(),
            'purpose' => AmbulanceTrip::PURPOSE_RUJUKAN_KELUAR,
            'origin' => fake()->address(),
            'destination' => fake()->address(),
            'departed_at' => fake()->dateTimeThisMonth()->format('Y-m-d H:i:s'),
            'returned_at' => null,
            'status' => AmbulanceTrip::STATUS_ONGOING,
        ];
    }

    /**
     * State uji: trip sudah selesai. Catatan - factory TIDAK menyinkronkan
     * status ambulans (itu kerja AmbulanceTripService); pakai hanya untuk
     * data list/riwayat, bukan untuk menguji gerbang.
     */
    public function completed(): self
    {
        return $this->state(fn () => [
            'returned_at' => now()->format('Y-m-d H:i:s'),
            'status' => AmbulanceTrip::STATUS_COMPLETED,
        ]);
    }

    /** State uji: trip dibatalkan sebelum berangkat tuntas. */
    public function cancelled(): self
    {
        return $this->state(fn () => ['status' => AmbulanceTrip::STATUS_CANCELLED]);
    }
}
