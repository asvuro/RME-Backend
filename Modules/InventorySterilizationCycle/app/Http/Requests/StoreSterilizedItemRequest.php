<?php

namespace Modules\InventorySterilizationCycle\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSterilizedItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cycle_id' => ['required', 'integer', 'exists:sterilization_cycles,id'],
            'item_name' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1'],
        ];
    }
}
