<?php

namespace Modules\LayananTelemedicineSession\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TelemedicineSessionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'visit_id' => $this->visit_id,
            'doctor_employee_id' => $this->doctor_employee_id,
            'scheduled_at' => $this->scheduled_at?->toIso8601String(),
            'started_at' => $this->started_at?->toIso8601String(),
            'ended_at' => $this->ended_at?->toIso8601String(),
            'session_url' => $this->session_url,
            'status' => $this->status,
            'consultation_notes' => $this->consultation_notes,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
