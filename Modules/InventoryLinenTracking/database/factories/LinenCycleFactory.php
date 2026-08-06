<?php

namespace Modules\InventoryLinenTracking\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\InventoryLinenTracking\Models\LinenCycle;
use Modules\InventoryLinenTracking\Models\LinenItem;

class LinenCycleFactory extends Factory
{
    protected $model = LinenCycle::class;

    public function definition(): array
    {
        return [
            'linen_item_id' => LinenItem::factory(),
            'status' => LinenCycle::STATUS_DIKIRIM_LONDRI,
            'sent_at' => now(),
            'received_at' => null,
            'quantity' => fake()->numberBetween(1, 20),
        ];
    }
}
