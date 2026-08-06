<?php

namespace Modules\FinanceGeneralLedger\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\FinanceGeneralLedger\Models\Account;
use Modules\FinanceGeneralLedger\Models\JournalEntry;
use Modules\FinanceGeneralLedger\Models\JournalEntryLine;

class JournalEntryLineFactory extends Factory
{
    protected $model = JournalEntryLine::class;

    public function definition(): array
    {
        return [
            'entry_id' => JournalEntry::factory(),
            'account_id' => Account::factory(),
            'debit' => 0,
            'kredit' => 0,
        ];
    }
}
