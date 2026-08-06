<?php

namespace Modules\LayananTelemedicineSession\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\LayananTelemedicineSession\Models\TelemedicineSession;

/**
 * PUT/PATCH = sunting atribut biasa ATAU pembatalan (satu-satunya transisi
 * status via endpoint ini). Karena itu 'status' hanya menerima 'cancelled':
 * transisi start/complete wajib lewat POST .../start dan .../complete agar
 * urutan mesin status ditegakkan service.
 */
class UpdateTelemedicineSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'scheduled_at' => ['sometimes', 'date'],
            'doctor_employee_id' => ['sometimes', 'integer', 'exists:employees,id'],
            'session_url' => ['sometimes', 'nullable', 'string', 'max:255'],
            'consultation_notes' => ['sometimes', 'nullable', 'string'],
            'status' => ['sometimes', 'string', 'in:'.TelemedicineSession::STATUS_CANCELLED],
        ];
    }
}
