<?php

namespace Modules\LayananDrugInteractionCheck\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\LayananDrugInteractionCheck\Models\DrugInteractionRule;

class UpdateDrugInteractionRuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'item_id_a' => ['sometimes', 'required', 'integer', 'exists:items,id'],
            'item_id_b' => ['sometimes', 'required', 'integer', 'exists:items,id', 'different:item_id_a'],
            'severity' => ['sometimes', 'required', 'string', Rule::in(DrugInteractionRule::SEVERITIES)],
            'clinical_note' => ['sometimes', 'required', 'string'],
        ];
    }
}
