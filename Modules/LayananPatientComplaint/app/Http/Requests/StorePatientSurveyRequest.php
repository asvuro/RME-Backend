<?php

namespace Modules\LayananPatientComplaint\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePatientSurveyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Field isian survei kepuasan. Aturan "satu kunjungan satu survei"
     * divalidasi sebagai gerbang bisnis di PatientSurveyService (pesan 422
     * berbahasa Indonesia, bukan error unique constraint mentah dari DB).
     */
    public function rules(): array
    {
        return [
            'visit_id' => ['required', 'integer', 'exists:visits,id'],
            // Skor kepuasan 1-5 (1 sangat tidak puas, 5 sangat puas).
            'satisfaction_score' => ['required', 'integer', 'min:1', 'max:5'],
            'feedback_text' => ['nullable', 'string'],
            'submitted_at' => ['required', 'date'],
        ];
    }
}
