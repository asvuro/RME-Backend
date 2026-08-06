<?php

namespace Modules\AuditInfectionSurveillance\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\AuditInfectionSurveillance\Models\DeviceDay;

class StoreDeviceDayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'visit_id' => ['required', 'integer', 'exists:visits,id'],
            'device_type' => ['required', 'string', Rule::in(DeviceDay::TYPES)],
            'inserted_at' => ['required', 'date'],
            'removed_at' => ['nullable', 'date', 'after_or_equal:inserted_at'],
        ];
    }
}
