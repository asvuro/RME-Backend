<?php

namespace Modules\InventoryBloodBag\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\InventoryBloodBag\Models\BloodBag;

class StoreBloodBagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'bag_number' => ['required', 'string', 'max:255', 'unique:blood_bags,bag_number'],
            'blood_type_id' => ['required', 'integer', 'exists:blood_types,id'],
            'volume_ml' => ['required', 'integer', 'min:1'],
            'collected_at' => ['required', 'date'],
            'expires_at' => ['required', 'date', 'after:collected_at'],
            'status' => ['sometimes', 'string', Rule::in([
                BloodBag::STATUS_IN_STOCK,
                BloodBag::STATUS_CROSSMATCH_RESERVED,
                BloodBag::STATUS_RELEASED,
                BloodBag::STATUS_TRANSFUSED,
                BloodBag::STATUS_RETURNED,
                BloodBag::STATUS_DISCARDED_EXPIRED,
            ])],
        ];
    }
}
