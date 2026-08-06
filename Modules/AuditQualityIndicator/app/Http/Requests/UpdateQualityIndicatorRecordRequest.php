<?php

namespace Modules\AuditQualityIndicator\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQualityIndicatorRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * indicator_id tidak divalidasi/diubah lewat edit: memindahkan catatan ke
     * indikator lain = hapus + buat ulang, agar gerbang duplikasi periode di
     * service tetap berlaku jujur untuk indikator lama dan baru.
     */
    public function rules(): array
    {
        return [
            'period_month' => ['sometimes', 'required', 'integer', 'between:1,12'],
            'period_year' => ['sometimes', 'required', 'integer', 'between:2000,2100'],
            'numerator' => ['sometimes', 'required', 'numeric', 'min:0'],
            'denominator' => ['sometimes', 'required', 'numeric', 'min:0'],
            'recorded_by' => ['nullable', 'integer', 'exists:employees,id'],
        ];
    }
}
