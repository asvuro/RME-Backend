<?php

namespace Modules\LayananLabAnalyzerOrder\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLabAnalyzerOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Hanya detail klinis sebelum pengiriman (service menolak 422 bila order
     * sudah lewat status ordered). Status/raw hasil/verifikasi TIDAK diedit
     * lewat sini - semuanya transisi khusus di LabAnalyzerOrderService.
     */
    public function rules(): array
    {
        return [
            'test_code' => ['sometimes', 'required', 'string', 'max:255'],
            'vendor_id' => ['sometimes', 'nullable', 'integer', 'exists:lab_analyzer_vendors,id'],
        ];
    }
}
