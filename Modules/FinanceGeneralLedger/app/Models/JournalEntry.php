<?php

namespace Modules\FinanceGeneralLedger\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Modules\FinanceGeneralLedger\Database\Factories\JournalEntryFactory;

/**
 * Header jurnal umum. Asal (source) bersifat polimorfik (mis. Invoice yang
 * terkunci) agar modul lain (Invoice, Payment, dst) bisa jadi pemicu tanpa
 * FK silang modul. Baris (lines) SELALU dibuat via
 * AccountingService::postEntry() — balance SUM(debit)=SUM(kredit) divalidasi
 * di service sebelum commit, bukan constraint DB (beda jenis akun bisa
 * ditambah kapan saja tanpa migrasi ulang).
 */
class JournalEntry extends Model
{
    use HasFactory;

    protected $fillable = ['date', 'description', 'source_type', 'source_id'];

    protected function casts(): array
    {
        return ['date' => 'date'];
    }

    public function lines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class, 'entry_id');
    }

    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    protected static function newFactory(): JournalEntryFactory
    {
        return JournalEntryFactory::new();
    }
}
