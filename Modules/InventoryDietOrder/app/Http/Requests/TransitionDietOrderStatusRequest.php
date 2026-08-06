<?php

namespace Modules\InventoryDietOrder\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\InventoryDietOrder\Models\DietOrder;

class TransitionDietOrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'string', Rule::in(DietOrder::STATUSES)],
        ];
    }
}
