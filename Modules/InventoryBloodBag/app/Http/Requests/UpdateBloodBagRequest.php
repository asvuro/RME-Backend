<?php

namespace Modules\InventoryBloodBag\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBloodBagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Status TIDAK divalidasi di sini dengan sengaja — perubahan status
     * kantong hanya boleh lewat gerbang BloodBankService (crossmatch,
     * release, markTransfused), bukan lewat update atribut biasa.
     */
    public function rules(): array
    {
        return [
            'bag_number' => ['sometimes', 'string', 'max:255', Rule::unique('blood_bags', 'bag_number')->ignore($this->route('blood_bag'))],
            'blood_type_id' => ['sometimes', 'integer', 'exists:blood_types,id'],
            'volume_ml' => ['sometimes', 'integer', 'min:1'],
            'collected_at' => ['sometimes', 'date'],
            'expires_at' => ['sometimes', 'date'],
        ];
    }
}
