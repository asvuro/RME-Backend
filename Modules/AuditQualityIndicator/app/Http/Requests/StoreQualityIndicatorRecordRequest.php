<?php

namespace Modules\AuditQualityIndicator\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQualityIndicatorRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * achieved_value sengaja tidak ada di sini: capaian dihitung dari
     * numerator/denominator oleh model, bukan diterima dari klien.
     */
    public function rules(): array
    {
        return [
            'indicator_id' => ['required', 'integer', 'exists:quality_indicators,id'],
            'period_month' => ['required', 'integer', 'between:1,12'],
            'period_year' => ['required', 'integer', 'between:2000,2100'],
            'numerator' => ['required', 'numeric', 'min:0'],
            'denominator' => ['required', 'numeric', 'min:0'],
            'recorded_by' => ['nullable', 'integer', 'exists:employees,id'],
        ];
    }
}
