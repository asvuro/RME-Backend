<?php

namespace Modules\FinanceGeneralLedger\Services;

use Illuminate\Support\Facades\DB;
use Modules\FinanceGeneralLedger\Models\Account;
use Modules\FinanceGeneralLedger\Models\JournalEntry;
use Modules\FinanceGeneralLedger\Models\JournalEntryLine;

/**
 * Gerbang tunggal penulisan jurnal. Prinsip double-entry: SUM(debit) HARUS
 * SAMA DENGAN SUM(kredit) per entry — divalidasi di sini (bukan constraint
 * DB, karena constraint SUM antar-baris tidak praktis dinyatakan di skema
 * MySQL) SEBELUM baris ditulis; entry & baris hanya commit bila balance.
 */
class AccountingService
{
    /**
     * Posting satu jurnal umum (header + baris) dalam satu transaksi.
     *
     * @param  array<int, array{account_id: int, debit?: float|string, kredit?: float|string}>  $lines
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException bila baris < 2, akun tidak
     *         ditemukan/nonaktif, atau SUM(debit) != SUM(kredit).
     */
    public function postEntry(
        array $lines,
        string $description = '',
        ?string $date = null,
        ?string $sourceType = null,
        ?int $sourceId = null,
    ): JournalEntry {
        abort_if(count($lines) < 2, 422, 'Jurnal minimal terdiri dari 2 baris (debit & kredit).');

        $totalDebit = 0.0;
        $totalKredit = 0.0;

        foreach ($lines as $line) {
            abort_if(! isset($line['account_id']), 422, 'Setiap baris jurnal wajib punya account_id.');

            $account = Account::query()->find($line['account_id']);
            abort_if($account === null, 422, "Akun #{$line['account_id']} tidak ditemukan.");
            abort_if(! $account->is_active, 422, "Akun {$account->code} tidak aktif.");

            $totalDebit += (float) ($line['debit'] ?? 0);
            $totalKredit += (float) ($line['kredit'] ?? 0);
        }

        // Bandingkan dalam sen (round 2 desimal) supaya galat pembulatan float
        // tidak salah menolak entry yang sebenarnya balance.
        abort_if(
            round($totalDebit, 2) !== round($totalKredit, 2),
            422,
            "Jurnal tidak balance: total debit {$totalDebit} != total kredit {$totalKredit}.",
        );

        return DB::transaction(function () use ($lines, $description, $date, $sourceType, $sourceId) {
            $entry = JournalEntry::create([
                'date' => $date ?? now()->toDateString(),
                'description' => $description,
                'source_type' => $sourceType,
                'source_id' => $sourceId,
            ]);

            foreach ($lines as $line) {
                JournalEntryLine::create([
                    'entry_id' => $entry->id,
                    'account_id' => $line['account_id'],
                    'debit' => $line['debit'] ?? 0,
                    'kredit' => $line['kredit'] ?? 0,
                ]);
            }

            return $entry->refresh()->load('lines.account');
        });
    }
}
