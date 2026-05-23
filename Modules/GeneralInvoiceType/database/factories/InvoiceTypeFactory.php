<?php

namespace Modules\GeneralInvoiceType\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\GeneralInvoiceType\Models\InvoiceType;

class InvoiceTypeFactory extends Factory
{
    protected $model = InvoiceType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->lexify('???'),
            'is_active' => true,
        ];
    }
}