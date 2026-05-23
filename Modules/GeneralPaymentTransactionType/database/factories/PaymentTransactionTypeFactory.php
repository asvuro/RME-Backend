<?php

namespace Modules\GeneralPaymentTransactionType\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralPaymentTransactionType\Models\PaymentTransactionType;

class PaymentTransactionTypeFactory extends Factory
{
    protected $model = PaymentTransactionType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}