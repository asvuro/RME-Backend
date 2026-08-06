<?php

namespace Modules\FinanceGeneralLedger\Listeners;

use App\Events\InvoiceLocked;
use Modules\FinanceGeneralLedger\Models\Account;
use Modules\FinanceGeneralLedger\Services\AccountingService;

/**
 * Posting otomatis jurnal saat tagihan dikunci (InvoiceService::lock/
 * markPaid/cancel semuanya dispatch InvoiceLocked — event ini tidak
 * membedakan alasannya sendiri, jadi kita baca $invoice->status).
 *
 * Aturan posting (versi sederhana, bukan port simgos2 - modul ini baru):
 * - status 'paid'   → Debit Kas, Kredit Pendapatan (kas sudah diterima).
 * - status lainnya  → Debit Piutang, Kredit Pendapatan (locked tapi belum
 *   lunas, mis. penutupan kasir dengan sisa tagihan ke penjamin).
 * - status 'cancelled' → TIDAK diposting (tidak ada pendapatan yang terjadi).
 *
 * total_amount 0 juga dilewati (postEntry menolak baris 0/0 sebagai
 * "tidak balance" secara sepele, dan tidak ada nilai ekonomis yang perlu
 * dicatat).
 */
class PostInvoiceLockedToLedger
{
    public function __construct(protected AccountingService $accounting) {}

    public function handle(InvoiceLocked $event): void
    {
        $invoice = $event->invoice;

        if ($invoice->status === 'cancelled') {
            return;
        }

        $amount = (float) $invoice->total_amount;

        if ($amount <= 0) {
            return;
        }

        $debitAccount = $invoice->status === 'paid'
            ? $this->kasAccount()
            : $this->piutangAccount();

        $this->accounting->postEntry(
            lines: [
                ['account_id' => $debitAccount->id, 'debit' => $amount, 'kredit' => 0],
                ['account_id' => $this->pendapatanAccount()->id, 'debit' => 0, 'kredit' => $amount],
            ],
            description: "Posting otomatis tagihan {$invoice->invoice_number}",
            date: now()->toDateString(),
            sourceType: get_class($invoice),
            sourceId: $invoice->id,
        );
    }

    /**
     * Akun default get-or-create by code — chart of accounts modul ini belum
     * tentu diisi manual sebelum transaksi klinis pertama terjadi, jadi
     * listener menjamin ketiga akun dasar selalu tersedia.
     */
    protected function kasAccount(): Account
    {
        return Account::query()->firstOrCreate(
            ['code' => '1-1000'],
            ['name' => 'Kas', 'type' => Account::TYPE_ASSET, 'is_active' => true],
        );
    }

    protected function piutangAccount(): Account
    {
        return Account::query()->firstOrCreate(
            ['code' => '1-1200'],
            ['name' => 'Piutang Pasien', 'type' => Account::TYPE_ASSET, 'is_active' => true],
        );
    }

    protected function pendapatanAccount(): Account
    {
        return Account::query()->firstOrCreate(
            ['code' => '4-1000'],
            ['name' => 'Pendapatan Layanan', 'type' => Account::TYPE_REVENUE, 'is_active' => true],
        );
    }
}
