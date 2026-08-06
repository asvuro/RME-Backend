<?php

namespace Modules\LayananMortuaryRecord\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMortuaryRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Status sengaja tidak divalidasi di sini: satu-satunya transisi
            // status ada di endpoint release(), bukan lewat update umum.
            'visit_id' => ['sometimes', 'nullable', 'integer', 'exists:visits,id'],
            'patient_id' => ['sometimes', 'integer', 'exists:patients,id'],
            'admitted_at' => ['sometimes', 'date'],
            'cause_of_death_notes' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
