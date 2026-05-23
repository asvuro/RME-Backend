<?php

namespace Modules\GeneralPaymentType\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralPaymentType\Models\PaymentType;

class PaymentTypeFactory extends Factory
{
    protected $model = PaymentType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}