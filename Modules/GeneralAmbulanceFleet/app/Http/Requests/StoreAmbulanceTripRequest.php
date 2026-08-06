<?php

namespace Modules\GeneralAmbulanceFleet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\GeneralAmbulanceFleet\Models\AmbulanceTrip;

class StoreAmbulanceTripRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ambulance_id' => ['required', 'integer', 'exists:ambulances,id'],
            // Nullable: jemput pasien dari alamat / antar jenazah belum tentu
            // terkait pasien terdaftar.
            'patient_id' => ['nullable', 'integer', 'exists:patients,id'],
            'driver_employee_id' => ['required', 'integer', 'exists:employees,id'],
            'purpose' => ['required', Rule::in([
                AmbulanceTrip::PURPOSE_RUJUKAN_KELUAR,
                AmbulanceTrip::PURPOSE_JEMPUT_PASIEN,
                AmbulanceTrip::PURPOSE_ANTAR_JENAZAH,
                AmbulanceTrip::PURPOSE_LAINNYA,
            ])],
            'origin' => ['required', 'string', 'max:255'],
            'destination' => ['required', 'string', 'max:255'],
            // Nullable: kalau tidak dikirim, service mencatat now() sebagai
            // waktu berangkat.
            'departed_at' => ['nullable', 'date'],
            // 'status' & 'returned_at' sengaja TIDAK divalidasi: trip baru selalu
            // 'ongoing' (dipaksa service) dan returned_at hanya diisi lewat
            // endpoint complete().
        ];
    }
}
