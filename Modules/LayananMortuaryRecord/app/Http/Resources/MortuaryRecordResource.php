<?php

namespace Modules\LayananMortuaryRecord\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MortuaryRecordResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'visit_id' => $this->visit_id,
            'patient_id' => $this->patient_id,
            'admitted_at' => $this->admitted_at?->toIso8601String(),
            'released_at' => $this->released_at?->toIso8601String(),
            'cause_of_death_notes' => $this->cause_of_death_notes,
            'released_to_name' => $this->released_to_name,
            'released_to_relationship' => $this->released_to_relationship,
            'released_by' => $this->released_by,
            'status' => $this->status,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
