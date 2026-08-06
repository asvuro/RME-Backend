<?php

namespace Modules\FinanceGeneralLedger\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\FinanceGeneralLedger\Database\Factories\JournalEntryLineFactory;

/**
 * Baris jurnal (debit/kredit) milik satu JournalEntry. Kolom 'kredit' sengaja
 * bukan 'credit' — mengikuti istilah spesifikasi modul ini apa adanya.
 */
class JournalEntryLine extends Model
{
    use HasFactory;

    protected $fillable = ['entry_id', 'account_id', 'debit', 'kredit'];

    protected function casts(): array
    {
        return [
            'debit' => 'decimal:2',
            'kredit' => 'decimal:2',
        ];
    }

    public function entry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'entry_id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    protected static function newFactory(): JournalEntryLineFactory
    {
        return JournalEntryLineFactory::new();
    }
}
