<?php

namespace Modules\InventoryLinenTracking\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLinenCycleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'linen_item_id' => ['required', 'integer', 'exists:linen_items,id'],
            'status' => ['sometimes', 'string', 'in:dikirim_londri,dicuci,kembali_bersih,rusak_hilang'],
            'sent_at' => ['sometimes', 'date'],
            'quantity' => ['sometimes', 'integer', 'min:1'],
        ];
    }
}
