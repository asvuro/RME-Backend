<?php

namespace Modules\PendaftaranSelfCheckin\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SelfCheckinQueueResource extends JsonResource
{
    /**
     * Payload sengaja pipih (id saja, tanpa relasi berlapis): konsumennya
     * layar kiosk/monitor antrian yang cuma butuh nomor + status + jam.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'patient_id' => $this->patient_id,
            'nik' => $this->nik,
            'queue_number' => $this->queue_number,
            'ward_id' => $this->ward_id,
            'queue_date' => $this->queue_date?->toDateString(),
            'checked_in_at' => $this->checked_in_at?->toIso8601String(),
            'status' => $this->status,
            'called_at' => $this->called_at?->toIso8601String(),
            'called_by' => $this->called_by,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
