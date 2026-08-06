<?php

namespace Modules\SystemTteDocument\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SignTteDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
        ];
    }
}
