<?php

namespace Modules\FinanceGeneralLedger\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\FinanceGeneralLedger\Models\JournalEntry;

class JournalEntryFactory extends Factory
{
    protected $model = JournalEntry::class;

    public function definition(): array
    {
        return [
            'date' => now()->toDateString(),
            'description' => fake()->sentence(),
            'source_type' => null,
            'source_id' => null,
        ];
    }
}
