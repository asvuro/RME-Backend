<?php

namespace Modules\Grup\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGroupReferralStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:accepted,rejected,in_progress,completed,cancelled'],
            'note' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
