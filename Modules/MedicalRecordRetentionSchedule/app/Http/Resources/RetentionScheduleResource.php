<?php

namespace Modules\MedicalRecordRetentionSchedule\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RetentionScheduleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'registration_id' => $this->registration_id,
            'patient_id' => $this->patient_id,
            'basis_date' => $this->basis_date?->toIso8601String(),
            'retention_years' => $this->retention_years,
            'retention_due_at' => $this->retention_due_at?->toDateString(),
            'status' => $this->status,
            'marked_by' => $this->marked_by,
            'marked_at' => $this->marked_at?->toIso8601String(),
            'notes' => $this->notes,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
