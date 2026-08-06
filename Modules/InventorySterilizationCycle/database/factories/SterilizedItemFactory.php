<?php

namespace Modules\InventorySterilizationCycle\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\InventorySterilizationCycle\Models\SterilizationCycle;
use Modules\InventorySterilizationCycle\Models\SterilizedItem;

class SterilizedItemFactory extends Factory
{
    protected $model = SterilizedItem::class;

    public function definition(): array
    {
        return [
            'cycle_id' => SterilizationCycle::factory()->passed(),
            'item_name' => fake()->randomElement(['Set Instrumen Bedah', 'Duk Steril', 'Gunting Jaringan', 'Klem Arteri']),
            'quantity' => fake()->numberBetween(1, 10),
            'expiry_date' => now()->addDays(30),
        ];
    }
}
