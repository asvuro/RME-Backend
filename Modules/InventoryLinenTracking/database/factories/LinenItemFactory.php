<?php

namespace Modules\InventoryLinenTracking\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\InventoryLinenTracking\Models\LinenItem;

class LinenItemFactory extends Factory
{
    protected $model = LinenItem::class;

    public function definition(): array
    {
        return [
            'linen_code' => fake()->unique()->bothify('LNN-#####'),
            'linen_type' => fake()->randomElement(['sprei', 'selimut', 'baju_ok', 'sarung_bantal', 'handuk']),
            'ward_id' => null,
        ];
    }
}
