<?php

namespace Modules\PegawaiRemunerasiJasaMedis\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRemunerationEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'source_type' => ['required', 'string', 'max:255'],
            'source_id' => ['required', 'integer'],
            'role' => ['required', 'string', 'max:255'],
            'gross_amount' => ['required', 'numeric', 'min:0.01'],
            'deduction_percentage' => ['sometimes', 'numeric', 'min:0', 'max:100'],
            'fixed_deduction' => ['sometimes', 'numeric', 'min:0'],
            'service_date' => ['required', 'date'],
            'notes' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
