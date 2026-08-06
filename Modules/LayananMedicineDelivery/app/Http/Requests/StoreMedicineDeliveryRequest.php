<?php

namespace Modules\LayananMedicineDelivery\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Rules PERSIS field yang dipakai MedicineDeliveryService::create():
 * pharmacy_dispense_id, patient_address, requested_at (opsional).
 * Status / kurir / delivered_at sengaja tidak divalidasi karena bukan
 * input klien - semuanya ditentukan gerbang bisnis di service.
 */
class StoreMedicineDeliveryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pharmacy_dispense_id' => ['required', 'integer', 'exists:pharmacy_dispenses,id'],
            'patient_address' => ['required', 'string', 'max:1000'],
            'requested_at' => ['nullable', 'date'],
        ];
    }
}
