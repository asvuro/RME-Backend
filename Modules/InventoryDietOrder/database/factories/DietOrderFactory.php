<?php

namespace Modules\InventoryDietOrder\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralEmployee\Models\Employee;
use Modules\InventoryDietOrder\Models\DietOrder;
use Modules\PendaftaranVisit\Models\Visit;

class DietOrderFactory extends Factory
{
    protected $model = DietOrder::class;

    public function definition(): array
    {
        return [
            'visit_id' => Visit::factory(),
            'diet_type' => fake()->randomElement(DietOrder::DIET_TYPES),
            'calorie_target' => fake()->numberBetween(1200, 2500),
            'allergy_notes' => null,
            'meal_schedule' => fake()->randomElement(DietOrder::MEAL_SCHEDULES),
            'ordered_by' => Employee::factory(),
            'status' => DietOrder::STATUS_ORDERED,
            'order_date' => now()->toDateString(),
        ];
    }
}
