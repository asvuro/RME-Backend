<?php

namespace Modules\GeneralGoodsReceiptType\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralGoodsReceiptType\Models\GoodsReceiptType;

class GoodsReceiptTypeFactory extends Factory
{
    protected $model = GoodsReceiptType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}