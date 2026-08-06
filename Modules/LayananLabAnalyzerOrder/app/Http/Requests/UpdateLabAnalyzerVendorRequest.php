<?php

namespace Modules\LayananLabAnalyzerOrder\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLabAnalyzerVendorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vendor_name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('lab_analyzer_vendors', 'vendor_name')->ignore($this->route('lab_analyzer_vendor')),
            ],
            'connection_notes' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
