<?php

namespace Modules\InventorySterilizationCycle\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSterilizationCycleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'machine_name' => ['required', 'string', 'max:255'],
            'temperature_celsius' => ['required', 'numeric'],
            'pressure_bar' => ['required', 'numeric'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
            'started_at' => ['required', 'date'],
            'completed_at' => ['nullable', 'date', 'after_or_equal:started_at'],
            'biological_indicator_result' => ['nullable', 'in:pending,negative,positive'],
            'status' => ['nullable', 'in:in_process,passed,failed'],
        ];
    }
}
