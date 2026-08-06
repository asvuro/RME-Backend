<?php

namespace Modules\LayananMedicineDelivery\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Satu-satunya field edit bebas adalah alamat tujuan. Status, kurir, dan
 * waktu tidak lewat sini - semuanya gerbang khusus di service
 * (assign-courier / mark-delivered), jadi tidak divalidasi di request ini.
 */
class UpdateMedicineDeliveryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_address' => ['sometimes', 'required', 'string', 'max:1000'],
        ];
    }
}
