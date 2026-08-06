<?php

namespace Modules\InventorySterilizationCycle\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSterilizedItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'item_name' => ['sometimes', 'string', 'max:255'],
            'quantity' => ['sometimes', 'integer', 'min:1'],
        ];
    }
}
