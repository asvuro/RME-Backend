<?php

namespace Modules\AuditInfectionSurveillance\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\AuditInfectionSurveillance\Models\DeviceDay;

class UpdateDeviceDayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'device_type' => ['sometimes', 'string', Rule::in(DeviceDay::TYPES)],
            'inserted_at' => ['sometimes', 'date'],
            // after_or_equal:inserted_at sengaja tidak dipakai di sini — nilai
            // lama inserted_at hanya bisa dibandingkan setelah digabung dengan
            // input; cek rentang gabungan ada di SurveillanceService.
            'removed_at' => ['sometimes', 'nullable', 'date'],
        ];
    }
}
