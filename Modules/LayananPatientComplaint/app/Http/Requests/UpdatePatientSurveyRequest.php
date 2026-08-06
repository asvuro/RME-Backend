<?php

namespace Modules\LayananPatientComplaint\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePatientSurveyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // visit_id boleh dikoreksi, tapi kunjungan tujuan tetap tidak
            // boleh sudah memiliki survei lain (gerbang di service).
            'visit_id' => ['sometimes', 'integer', 'exists:visits,id'],
            'satisfaction_score' => ['sometimes', 'integer', 'min:1', 'max:5'],
            'feedback_text' => ['sometimes', 'nullable', 'string'],
            'submitted_at' => ['sometimes', 'date'],
        ];
    }
}
