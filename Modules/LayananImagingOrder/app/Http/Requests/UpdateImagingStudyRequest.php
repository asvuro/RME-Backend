<?php

namespace Modules\LayananImagingOrder\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateImagingStudyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Ordernya tidak boleh diganti-ganti lewat edit: hubungannya bagian
            // dari pencatatan pengerjaan, bukan atribut hasil.
            'study_instance_uid' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
                Rule::unique('imaging_studies', 'study_instance_uid')->ignore($this->route('imaging_study')),
            ],
            'performed_at' => ['sometimes', 'date'],
            'findings_summary' => ['sometimes', 'nullable', 'string'],
            'report_url' => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }
}
