<?php

namespace Modules\AuditInfectionSurveillance\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\AuditInfectionSurveillance\Models\InfectionCase;

class UpdateInfectionCaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // visit_id tidak boleh diubah lewat update — memindahkan kasus ke
        // kunjungan lain adalah koreksi data epidemiologi, bukan edit biasa;
        // gerbang rujukan di SurveillanceService mengasumsikan kunjungan tetap.
        return [
            'infection_type' => ['sometimes', 'string', Rule::in(InfectionCase::TYPES)],
            'diagnosed_at' => ['sometimes', 'date'],
            'related_device_day_id' => ['sometimes', 'nullable', 'integer', 'exists:device_days,id'],
        ];
    }
}
