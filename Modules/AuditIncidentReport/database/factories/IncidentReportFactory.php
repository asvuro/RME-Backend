<?php

namespace Modules\AuditIncidentReport\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Modules\AuditIncidentReport\Models\IncidentReport;
use Modules\AuditIncidentReport\Services\IncidentReportService;

class IncidentReportFactory extends Factory
{
    protected $model = IncidentReport::class;

    public function definition(): array
    {
        // Grade/status/SLA dihitung lewat logika service yang sama dengan
        // jalur create() agar baris uji selalu konsisten dgn matriks 5x5.
        $impact = fake()->numberBetween(1, 5);
        $probability = fake()->numberBetween(1, 5);
        $category = fake()->randomElement(IncidentReport::CATEGORIES);
        $occurredAt = fake()->dateTimeBetween('-30 days');
        $grade = IncidentReportService::gradeFromScores($impact, $probability);

        return [
            'visit_id' => null,
            'patient_id' => null,
            'incident_category' => $category,
            'description' => fake()->sentence(),
            'occurred_at' => $occurredAt,
            'reported_by' => null,
            'impact_score' => $impact,
            'probability_score' => $probability,
            'risk_grade' => $grade,
            'status' => IncidentReport::STATUS_REPORTED,
            'sla_due_at' => IncidentReportService::slaDueAtFor($grade, $category, Carbon::instance($occurredAt)),
        ];
    }
}
