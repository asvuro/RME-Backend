<?php

namespace Modules\AuditQualityIndicator\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\AuditQualityIndicator\Models\QualityIndicator;

class UpdateQualityIndicatorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('quality_indicator')?->id;

        return [
            'code' => ['sometimes', 'required', 'string', 'max:50', Rule::unique('quality_indicators', 'code')->ignore($id)],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'unit_of_measure' => ['sometimes', 'required', 'string', 'max:20'],
            'target_value' => ['nullable', 'numeric', 'min:0'],
            'category' => ['sometimes', 'required', 'string', Rule::in(QualityIndicator::CATEGORIES)],
        ];
    }
}
