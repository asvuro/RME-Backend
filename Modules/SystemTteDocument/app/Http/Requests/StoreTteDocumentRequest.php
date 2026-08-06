<?php

namespace Modules\SystemTteDocument\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTteDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ref_type' => ['required', 'string', 'max:30'],
            'ref_id' => ['required', 'integer', 'min:1'],
            'content' => ['nullable', 'array'],
        ];
    }
}
