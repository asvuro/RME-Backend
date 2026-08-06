<?php

namespace Modules\LayananPatientComplaint\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\LayananPatientComplaint\Models\PatientSurvey;

class PatientSurveyFactory extends Factory
{
    protected $model = PatientSurvey::class;

    public function definition(): array
    {
        return [
            // Setiap survei memakai kunjungan sendiri karena visit_id unik.
            'visit_id' => \Modules\PendaftaranVisit\Models\Visit::factory(),
            'satisfaction_score' => fake()->numberBetween(1, 5),
            'feedback_text' => fake()->optional()->paragraph(),
            'submitted_at' => fake()->dateTimeBetween('-1 month', 'now')->format('Y-m-d H:i:s'),
        ];
    }
}
