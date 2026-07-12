<?php

namespace Modules\LayananPharmacyDispense\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Sejak #5 (audit write-path): quantity/status/dispensed_by/dispensed_at
 * TIDAK lagi dipakai controller -- semuanya hasil gerbang bisnis di
 * DispenseService::dispense(), bukan input klien. Rules disamakan dengan
 * yang benar-benar dibaca controller (cuma prescription_id) supaya endpoint
 * ini bisa dipanggil (sebelumnya validasi minta field yang sudah tak dipakai).
 */
class StorePharmacyDispenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'prescription_id' => ['required', 'integer', 'exists:prescriptions,id'],
        ];
    }
}
