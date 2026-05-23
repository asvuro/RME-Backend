<?php

namespace Modules\GeneralGoodsReceiptCancellationReason\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralGoodsReceiptCancellationReason\Models\GoodsReceiptCancellationReason;

class GoodsReceiptCancellationReasonFactory extends Factory
{
    protected $model = GoodsReceiptCancellationReason::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}