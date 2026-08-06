<?php

namespace Modules\InventoryDietOrder\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DietOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'visit_id' => $this->visit_id,
            'diet_type' => $this->diet_type,
            'calorie_target' => $this->calorie_target,
            'allergy_notes' => $this->allergy_notes,
            'meal_schedule' => $this->meal_schedule,
            'ordered_by' => $this->ordered_by,
            'status' => $this->status,
            'order_date' => $this->order_date?->toDateString(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
