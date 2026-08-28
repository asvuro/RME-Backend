<?php

namespace Modules\Grup\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Grup\Models\Branch;

class StoreGroupReferralRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $local = Branch::query()->where('is_local', true)->where('status', 'active')->first();

        return [
            'destination_branch_id' => [
                'required', 'uuid',
                Rule::exists('grup_branches', 'hub_branch_id')->where(fn ($query) => $query
                    ->where('group_id', $local?->group_id ?? 0)->where('status', 'active')->where('is_local', false)),
            ],
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'reason' => ['required', 'string', 'min:5', 'max:5000'],
            'clinical_summary' => ['nullable', 'string', 'max:20000'],
        ];
    }
}
