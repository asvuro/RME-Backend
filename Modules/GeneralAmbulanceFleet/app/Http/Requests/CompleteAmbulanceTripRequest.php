<?php

namespace Modules\GeneralAmbulanceFleet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompleteAmbulanceTripRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Nullable: kalau tidak dikirim, service memakai now() sebagai
            // waktu kembali.
            'returned_at' => ['nullable', 'date'],
        ];
    }
}
