<?php

namespace Modules\AuditIncidentReport\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IncidentReportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'visit_id' => $this->visit_id,
            'patient_id' => $this->patient_id,
            'incident_category' => $this->incident_category,
            'description' => $this->description,
            'occurred_at' => $this->occurred_at?->toIso8601String(),
            'reported_by' => $this->reported_by,
            'impact_score' => $this->impact_score,
            'probability_score' => $this->probability_score,
            // Hasil kalkulasi service — bukan input.
            'risk_grade' => $this->risk_grade,
            'status' => $this->status,
            'sla_due_at' => $this->sla_due_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            // Ringkasan relasi (aman bila tidak di-load).
            'visit_number' => $this->whenLoaded('visit', fn () => $this->visit?->visit_number),
            'patient_name' => $this->whenLoaded('patient', fn () => $this->patient?->name),
            'reported_by_name' => $this->whenLoaded('reportedBy', fn () => $this->reportedBy?->name),
        ];
    }
}
