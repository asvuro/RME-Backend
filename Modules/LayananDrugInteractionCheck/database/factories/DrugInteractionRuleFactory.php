<?php

namespace Modules\LayananDrugInteractionCheck\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\InventoryItem\Database\Factories\ItemFactory;
use Modules\LayananDrugInteractionCheck\Models\DrugInteractionRule;

class DrugInteractionRuleFactory extends Factory
{
    protected $model = DrugInteractionRule::class;

    public function definition(): array
    {
        return [
            // ItemFactory dipanggil eksplisit (bukan Item::factory()) karena model
            // Item tidak meng-override newFactory() ke namespace modulnya.
            'item_id_a' => ItemFactory::new(),
            'item_id_b' => ItemFactory::new(),
            'severity' => fake()->randomElement(DrugInteractionRule::SEVERITIES),
            'clinical_note' => fake()->sentence(),
        ];
    }
}
