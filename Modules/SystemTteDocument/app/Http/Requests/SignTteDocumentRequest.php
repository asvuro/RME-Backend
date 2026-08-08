<?php

namespace Modules\SystemTteDocument\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SignTteDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // employee_id SENGAJA tidak divalidasi/diterima dari klien -- signer
        // selalu dari profil pegawai milik user yang login (lihat controller),
        // supaya petugas tidak bisa menandatangani dokumen atas nama orang lain.
        return [];
    }
}
