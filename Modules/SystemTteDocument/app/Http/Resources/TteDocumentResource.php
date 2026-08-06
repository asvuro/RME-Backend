<?php

namespace Modules\SystemTteDocument\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TteDocumentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ref_type' => $this->ref_type,
            'ref_id' => $this->ref_id,
            'status' => $this->status,
            'content' => $this->content,
            'document_hash' => $this->document_hash,
            'signed_by' => $this->signed_by,
            'signed_at' => $this->signed_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
