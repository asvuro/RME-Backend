<?php

namespace Modules\LayananTelemedicineSession\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Body opsional POST .../complete: cukup catatan konsultasi akhir.
 * Status/ended_at ditetapkan gerbang service, bukan input klien.
 */
class CompleteTelemedicineSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'consultation_notes' => ['nullable', 'string'],
        ];
    }
}
