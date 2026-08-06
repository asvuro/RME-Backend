<?php

namespace Modules\InventoryDietOrder\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\InventoryDietOrder\Models\DietOrder;

class UpdateDietOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'diet_type' => ['sometimes', 'string', Rule::in(DietOrder::DIET_TYPES)],
            'calorie_target' => ['nullable', 'integer', 'min:0'],
            'allergy_notes' => ['nullable', 'string'],
            'meal_schedule' => ['sometimes', 'string', Rule::in(DietOrder::MEAL_SCHEDULES)],
            'order_date' => ['sometimes', 'date'],
        ];
    }
}
