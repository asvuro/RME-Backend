<?php

namespace Modules\LayananMortuaryRecord\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMortuaryRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Nullable: jenazah non rawat-inap tidak punya kunjungan.
            'visit_id' => ['nullable', 'integer', 'exists:visits,id'],
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'admitted_at' => ['required', 'date'],
            'cause_of_death_notes' => ['nullable', 'string'],
        ];
    }
}
