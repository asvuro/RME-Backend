<?php

namespace Modules\PegawaiRemunerasiJasaMedis\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRemunerationEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id' => ['sometimes', 'integer', 'exists:employees,id'],
            'source_type' => ['sometimes', 'string', 'max:255'],
            'source_id' => ['sometimes', 'integer'],
            'role' => ['sometimes', 'string', 'max:255'],
            'gross_amount' => ['sometimes', 'numeric', 'min:0.01'],
            'deduction_percentage' => ['sometimes', 'numeric', 'min:0', 'max:100'],
            'fixed_deduction' => ['sometimes', 'numeric', 'min:0'],
            'service_date' => ['sometimes', 'date'],
            'notes' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
