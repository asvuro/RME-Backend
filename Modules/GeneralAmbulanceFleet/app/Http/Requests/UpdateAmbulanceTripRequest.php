<?php

namespace Modules\GeneralAmbulanceFleet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\GeneralAmbulanceFleet\Models\AmbulanceTrip;

class UpdateAmbulanceTripRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['sometimes', 'nullable', 'integer', 'exists:patients,id'],
            'driver_employee_id' => ['sometimes', 'integer', 'exists:employees,id'],
            'purpose' => ['sometimes', Rule::in([
                AmbulanceTrip::PURPOSE_RUJUKAN_KELUAR,
                AmbulanceTrip::PURPOSE_JEMPUT_PASIEN,
                AmbulanceTrip::PURPOSE_ANTAR_JENAZAH,
                AmbulanceTrip::PURPOSE_LAINNYA,
            ])],
            'origin' => ['sometimes', 'string', 'max:255'],
            'destination' => ['sometimes', 'string', 'max:255'],
            'departed_at' => ['sometimes', 'date'],
            // 'ambulance_id' sengaja TIDAK divalidasi: ganti kendaraan di tengah
            // trip akan mendesinkronkan status kedua ambulans (yang lama tetap
            // in_use). Ganti kendaraan = batalkan dan buat trip baru.
            // 'status' & 'returned_at' hanya lewat endpoint complete().
        ];
    }
}
