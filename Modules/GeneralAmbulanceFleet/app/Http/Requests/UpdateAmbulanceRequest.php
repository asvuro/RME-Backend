<?php

namespace Modules\GeneralAmbulanceFleet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\GeneralAmbulanceFleet\Models\Ambulance;

class UpdateAmbulanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vehicle_code' => ['sometimes', 'string', 'max:50', Rule::unique('ambulances', 'vehicle_code')->ignore($this->route('ambulance'))],
            'plate_number' => ['sometimes', 'string', 'max:20'],
            // Nilai diizinkan di level validasi (schema), tapi legalitas transisi
            // (mis. manual ke in_use, atau mengubah status ambulans yang sedang
            // trip) dinilai oleh gerbang AmbulanceService::updateDetails().
            'status' => ['sometimes', Rule::in([
                Ambulance::STATUS_AVAILABLE,
                Ambulance::STATUS_IN_USE,
                Ambulance::STATUS_MAINTENANCE,
            ])],
        ];
    }
}
