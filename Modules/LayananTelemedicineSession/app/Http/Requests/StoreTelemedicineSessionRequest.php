<?php

namespace Modules\LayananTelemedicineSession\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Rules persis field yang dibaca service::schedule(). status/dokter pemeriksa
 * final/started_at dsb BUKAN input klien — semuanya hasil gerbang bisnis di
 * TelemedicineSessionService, jadi tidak divalidasi di sini.
 */
class StoreTelemedicineSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'visit_id' => ['required', 'integer', 'exists:visits,id'],
            'doctor_employee_id' => ['required', 'integer', 'exists:employees,id'],
            'scheduled_at' => ['required', 'date'],
            'session_url' => ['nullable', 'string', 'max:255'],
        ];
    }
}
