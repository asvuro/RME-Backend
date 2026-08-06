<?php

namespace Modules\InventoryBloodBag\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\InventoryBloodBag\Models\BloodBag;
use Modules\KemkesBloodType\Models\BloodType;

class BloodBagFactory extends Factory
{
    protected $model = BloodBag::class;

    public function definition(): array
    {
        return [
            'bag_number' => fake()->unique()->bothify('BB-########'),
            'blood_type_id' => BloodType::factory(),
            'volume_ml' => 350,
            'collected_at' => now()->subDays(3),
            'expires_at' => now()->addDays(32),
            'status' => BloodBag::STATUS_IN_STOCK,
        ];
    }
}
