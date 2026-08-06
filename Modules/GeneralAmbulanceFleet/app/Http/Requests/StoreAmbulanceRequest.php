<?php

namespace Modules\GeneralAmbulanceFleet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAmbulanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vehicle_code' => ['required', 'string', 'max:50', 'unique:ambulances,vehicle_code'],
            'plate_number' => ['required', 'string', 'max:20'],
            // 'status' sengaja TIDAK divalidasi di sini: armada baru selalu lahir
            // 'available' (dipaksa AmbulanceService::register). in_use/maintenance
            // hanya boleh muncul dari kejadian bisnis (mulai trip / servis),
            // bukan input master data.
        ];
    }
}
