<?php

namespace Modules\LayananDrugInteractionCheck\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\LayananDrugInteractionCheck\Models\DrugInteractionRule;

class StoreDrugInteractionRuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'item_id_a' => ['required', 'integer', 'exists:items,id'],
            'item_id_b' => ['required', 'integer', 'exists:items,id', 'different:item_id_a'],
            'severity' => ['required', 'string', Rule::in(DrugInteractionRule::SEVERITIES)],
            // Catatan klinis wajib: tanpa konteks, temuan "major" tidak bisa ditindaklanjuti petugas.
            'clinical_note' => ['required', 'string'],
        ];
    }
}
