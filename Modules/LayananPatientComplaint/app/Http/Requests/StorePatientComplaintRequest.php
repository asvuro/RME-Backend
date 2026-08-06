<?php

namespace Modules\LayananPatientComplaint\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\LayananPatientComplaint\Models\PatientComplaint;

class StorePatientComplaintRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Hanya field isian awal komplain. status/handled_by/resolution_notes
     * sengaja TIDAK divalidasi di sini karena dikelola gerbang
     * PatientComplaintService (store selalu melahirkan status 'baru');
     * menerima ketiganya berarti membuka celah bypass state machine.
     */
    public function rules(): array
    {
        return [
            'patient_id' => ['nullable', 'integer', 'exists:patients,id'],
            'visit_id' => ['nullable', 'integer', 'exists:visits,id'],
            'category' => ['required', 'string', 'in:' . implode(',', PatientComplaint::CATEGORIES)],
            'description' => ['required', 'string'],
            'submitted_at' => ['required', 'date'],
        ];
    }
}
