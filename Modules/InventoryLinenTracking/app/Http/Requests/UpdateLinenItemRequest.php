<?php

namespace Modules\InventoryLinenTracking\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLinenItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $linenItem = $this->route('linen_item');

        return [
            'linen_code' => ['sometimes', 'string', 'max:255', Rule::unique('linen_items', 'linen_code')->ignore($linenItem)],
            'linen_type' => ['sometimes', 'string', 'max:255'],
            'ward_id' => ['nullable', 'integer', 'exists:wards,id'],
        ];
    }
}
