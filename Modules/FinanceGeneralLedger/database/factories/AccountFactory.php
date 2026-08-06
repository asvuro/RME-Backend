<?php

namespace Modules\FinanceGeneralLedger\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\FinanceGeneralLedger\Models\Account;

class AccountFactory extends Factory
{
    protected $model = Account::class;

    public function definition(): array
    {
        return [
            'code' => fake()->unique()->numerify('#####'),
            'name' => fake()->words(3, true),
            'type' => fake()->randomElement(['asset', 'liability', 'equity', 'revenue', 'expense']),
            'is_active' => true,
        ];
    }
}
