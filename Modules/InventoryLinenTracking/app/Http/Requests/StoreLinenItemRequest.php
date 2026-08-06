<?php

namespace Modules\InventoryLinenTracking\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLinenItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'linen_code' => ['required', 'string', 'max:255', 'unique:linen_items,linen_code'],
            'linen_type' => ['required', 'string', 'max:255'],
            'ward_id' => ['nullable', 'integer', 'exists:wards,id'],
        ];
    }
}
