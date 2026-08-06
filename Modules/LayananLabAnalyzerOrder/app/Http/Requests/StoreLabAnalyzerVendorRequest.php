<?php

namespace Modules\LayananLabAnalyzerOrder\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLabAnalyzerVendorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vendor_name' => ['required', 'string', 'max:255', 'unique:lab_analyzer_vendors,vendor_name'],
            'connection_notes' => ['nullable', 'string'],
        ];
    }
}
