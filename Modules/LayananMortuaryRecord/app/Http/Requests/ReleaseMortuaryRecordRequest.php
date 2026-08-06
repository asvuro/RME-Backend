<?php

namespace Modules\LayananMortuaryRecord\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReleaseMortuaryRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'released_at' => ['required', 'date'],
            'released_to_name' => ['required', 'string', 'max:255'],
            'released_to_relationship' => ['nullable', 'string', 'max:255'],
            'released_by' => ['required', 'integer', 'exists:employees,id'],
        ];
    }
}
