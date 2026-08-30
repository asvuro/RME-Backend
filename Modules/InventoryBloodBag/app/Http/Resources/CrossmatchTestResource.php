<?php

namespace Modules\InventoryBloodBag\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CrossmatchTestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'blood_bag_id' => $this->blood_bag_id,
            'patient_id' => $this->patient_id,
            'major_result' => $this->major_result,
            'minor_result' => $this->minor_result,
            'auto_control' => $this->auto_control,
            'is_compatible' => $this->is_compatible,
            'tested_by' => $this->tested_by,
            'tested_at' => $this->tested_at?->toIso8601String(),
            'reserved_until' => $this->reserved_until?->toIso8601String(),
            'blood_bag_status' => $this->whenLoaded('bloodBag', fn () => $this->bloodBag?->status),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
