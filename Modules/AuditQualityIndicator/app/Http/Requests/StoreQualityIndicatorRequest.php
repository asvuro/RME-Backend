<?php

namespace Modules\AuditQualityIndicator\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\AuditQualityIndicator\Models\QualityIndicator;

class StoreQualityIndicatorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** Persis field master QualityIndicator — tidak ada field lain di luar spesifikasi. */
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:50', 'unique:quality_indicators,code'],
            'name' => ['required', 'string', 'max:255'],
            'unit_of_measure' => ['required', 'string', 'max:20'],
            'target_value' => ['nullable', 'numeric', 'min:0'],
            'category' => ['required', 'string', Rule::in(QualityIndicator::CATEGORIES)],
        ];
    }
}
