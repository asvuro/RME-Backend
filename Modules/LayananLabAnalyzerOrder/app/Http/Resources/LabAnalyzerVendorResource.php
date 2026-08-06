<?php

namespace Modules\LayananLabAnalyzerOrder\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LabAnalyzerVendorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'vendor_name' => $this->vendor_name,
            'connection_notes' => $this->connection_notes,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
