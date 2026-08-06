<?php

namespace Modules\FinanceGeneralLedger\Tests\Feature;

use App\Events\InvoiceLocked;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\FinanceGeneralLedger\Models\Account;
use Modules\FinanceGeneralLedger\Models\JournalEntry;
use Modules\PembayaranInvoice\Models\Invoice;
use Tests\TestCase;

/**
 * Listener PostInvoiceLockedToLedger: memastikan efek samping non-kritis
 * posting jurnal otomatis dari event App\Events\InvoiceLocked benar secara
 * kalkulasi (angka debit/kredit == total_amount invoice, akun sesuai status).
 */
class PostInvoiceLockedToLedgerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('petugas');
        $this->actingAs($user, 'sanctum');
    }

    public function test_invoice_locked_status_paid_posting_debit_kas_kredit_pendapatan(): void
    {
        $invoice = Invoice::factory()->create([
            'total_amount' => 250000,
            'status' => 'paid',
            'is_locked' => true,
        ]);

        InvoiceLocked::dispatch($invoice);

        $entry = JournalEntry::query()->where('source_type', Invoice::class)->where('source_id', $invoice->id)->firstOrFail();
        $entry->load('lines.account');

        $kas = Account::query()->where('code', '1-1000')->firstOrFail();
        $pendapatan = Account::query()->where('code', '4-1000')->firstOrFail();

        $debitLine = $entry->lines->firstWhere('account_id', $kas->id);
        $kreditLine = $entry->lines->firstWhere('account_id', $pendapatan->id);

        $this->assertNotNull($debitLine, 'Baris debit Kas harus ada untuk invoice paid.');
        $this->assertSame('250000.00', (string) $debitLine->debit);
        $this->assertSame('0.00', (string) $debitLine->kredit);
        $this->assertSame('250000.00', (string) $kreditLine->kredit);
    }

    public function test_invoice_locked_status_open_posting_debit_piutang(): void
    {
        $invoice = Invoice::factory()->create([
            'total_amount' => 100000,
            'status' => 'open',
            'is_locked' => true,
        ]);

        InvoiceLocked::dispatch($invoice);

        $piutang = Account::query()->where('code', '1-1200')->firstOrFail();

        $entry = JournalEntry::query()->where('source_id', $invoice->id)->firstOrFail();
        $debitLine = $entry->load('lines')->lines->firstWhere('account_id', $piutang->id);

        $this->assertNotNull($debitLine, 'Invoice belum lunas harus posting ke Piutang, bukan Kas.');
        $this->assertSame('100000.00', (string) $debitLine->debit);
    }

    public function test_invoice_locked_status_cancelled_tidak_diposting(): void
    {
        $invoice = Invoice::factory()->create([
            'total_amount' => 100000,
            'status' => 'cancelled',
            'is_locked' => true,
        ]);

        InvoiceLocked::dispatch($invoice);

        $this->assertDatabaseCount('journal_entries', 0);
    }
}
