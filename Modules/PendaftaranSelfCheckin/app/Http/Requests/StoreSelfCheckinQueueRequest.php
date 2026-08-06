<?php

namespace Modules\PendaftaranSelfCheckin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSelfCheckinQueueRequest extends FormRequest
{
    /**
     * Sengaja TANPA authorize berbasis role: endpoint check-in dipakai device
     * kiosk dengan token service account (lihat komentar di routes/api.php).
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Hanya field yang benar-benar dikonsumsi SelfCheckinService::checkIn().
     * 'status' dan 'queue_number' SENGAJA tidak ada di sini: keduanya
     * ditetapkan service (antrian baru selalu 'waiting', nomor dari generator
     * harian) supaya state machine tidak bisa dilewati dari payload kiosk.
     */
    public function rules(): array
    {
        return [
            // Identitas minimum: pasien terdaftar ATAU NIK, minimal salah satu.
            'patient_id' => ['nullable', 'integer', 'exists:patients,id', 'required_without:nik'],
            // NIK KTP: tepat 16 digit, muat di kolom varchar(16).
            'nik' => ['nullable', 'string', 'digits:16', 'required_without:patient_id'],
            // Poli tujuan boleh kosong: anjungan umum tanpa pemilihan poli.
            'ward_id' => ['nullable', 'integer', 'exists:wards,id'],
        ];
    }
}
