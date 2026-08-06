<?php

namespace Modules\LayananMedicineDelivery\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicineDeliveryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'pharmacy_dispense_id' => $this->pharmacy_dispense_id,
            'patient_address' => $this->patient_address,
            'courier_employee_id' => $this->courier_employee_id,
            'status' => $this->status,
            'requested_at' => $this->requested_at?->toIso8601String(),
            'delivered_at' => $this->delivered_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
