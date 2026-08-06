<?php

namespace Modules\LayananPatientComplaint\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\LayananPatientComplaint\Models\PatientComplaint;

class PatientComplaintFactory extends Factory
{
    protected $model = PatientComplaint::class;

    public function definition(): array
    {
        return [
            'patient_id' => \Modules\GeneralPatient\Models\Patient::factory(),
            'visit_id' => \Modules\PendaftaranVisit\Models\Visit::factory(),
            'category' => fake()->randomElement(PatientComplaint::CATEGORIES),
            'description' => fake()->paragraph(),
            'submitted_at' => fake()->dateTimeBetween('-1 month', 'now')->format('Y-m-d H:i:s'),
            // Default lahir sebagai komplain baru tanpa penanggung jawab;
            // status lanjutan diuji lewat gerbang service, bukan factory.
            'status' => PatientComplaint::STATUS_BARU,
            'handled_by' => null,
            'resolution_notes' => null,
        ];
    }
}
