<?php

namespace Modules\LayananLabAnalyzerOrder\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LabAnalyzerOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'visit_id' => $this->visit_id,
            'vendor_id' => $this->vendor_id,
            'test_code' => $this->test_code,
            'ordered_by' => $this->ordered_by,
            'ordered_at' => $this->ordered_at?->toIso8601String(),
            'status' => $this->status,
            'raw_result_text' => $this->raw_result_text,
            'verified_by' => $this->verified_by,
            'verified_at' => $this->verified_at?->toIso8601String(),
            'visit' => $this->whenLoaded('visit', fn () => [
                'id' => $this->visit->id,
                'visit_number' => $this->visit->visit_number,
                'status' => $this->visit->status,
            ]),
            'vendor' => $this->whenLoaded('vendor', fn () => $this->vendor ? [
                'id' => $this->vendor->id,
                'vendor_name' => $this->vendor->vendor_name,
            ] : null),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
