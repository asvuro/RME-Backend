<?php

namespace Modules\InventorySterilizationCycle\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSterilizationCycleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'machine_name' => ['sometimes', 'string', 'max:255'],
            'temperature_celsius' => ['sometimes', 'numeric'],
            'pressure_bar' => ['sometimes', 'numeric'],
            'duration_minutes' => ['sometimes', 'integer', 'min:1'],
            'started_at' => ['sometimes', 'date'],
            'completed_at' => ['nullable', 'date', 'after_or_equal:started_at'],
            'biological_indicator_result' => ['sometimes', 'in:pending,negative,positive'],
            'status' => ['sometimes', 'in:in_process,passed,failed'],
        ];
    }
}
