<?php

namespace Modules\LayananEarlyWarningScore\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralEmployee\Models\Employee;
use Modules\LayananEarlyWarningScore\Models\VitalSignObservation;
use Modules\PendaftaranVisit\Models\Visit;

class VitalSignObservationFactory extends Factory
{
    protected $model = VitalSignObservation::class;

    /**
     * Nilai default = semua parameter berada di band skor 0 NEWS2
     * (total_score 0 / risk_level "rendah" jika dihitung), supaya test yang
     * butuh skenario khusus cukup meng-override satu parameter.
     */
    public function definition(): array
    {
        return [
            'visit_id' => Visit::factory(),
            'respiratory_rate' => 14,
            'spo2' => 98,
            'systolic_bp' => 120,
            'pulse_rate' => 72,
            'consciousness_level' => VitalSignObservation::CONSCIOUSNESS_ALERT,
            'temperature_celsius' => 36.8,
            'recorded_by' => Employee::factory(),
            'recorded_at' => now(),
            // Kolom turunan: null kecuali test/service yang mengisinya secara eksplisit.
            'total_score' => null,
            'risk_level' => null,
        ];
    }
}
