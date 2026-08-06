<?php

namespace Modules\LayananImagingOrder\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ImagingStudyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'imaging_order_id' => $this->imaging_order_id,
            'study_instance_uid' => $this->study_instance_uid,
            'performed_at' => $this->performed_at?->toIso8601String(),
            'findings_summary' => $this->findings_summary,
            'report_url' => $this->report_url,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
