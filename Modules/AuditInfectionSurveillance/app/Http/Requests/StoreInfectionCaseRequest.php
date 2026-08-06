<?php

namespace Modules\AuditInfectionSurveillance\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\AuditInfectionSurveillance\Models\InfectionCase;

class StoreInfectionCaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'visit_id' => ['required', 'integer', 'exists:visits,id'],
            'infection_type' => ['required', 'string', Rule::in(InfectionCase::TYPES)],
            'diagnosed_at' => ['required', 'date'],
            'related_device_day_id' => ['nullable', 'integer', 'exists:device_days,id'],
        ];
    }
}
