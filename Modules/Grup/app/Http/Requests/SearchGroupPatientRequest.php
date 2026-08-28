<?php

namespace Modules\Grup\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchGroupPatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'branch_id' => ['nullable', 'uuid'],
            'q' => ['required', 'string', 'min:3', 'max:100'],
            'page' => ['sometimes', 'integer', 'min:1', 'max:1000'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:50'],
        ];
    }
}
