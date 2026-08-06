<?php

namespace Modules\FinanceGeneralLedger\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\FinanceGeneralLedger\Models\Account;
use Modules\FinanceGeneralLedger\Models\JournalEntryLine;
use Modules\FinanceGeneralLedger\Services\AccountingService;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

/**
 * Gerbang inti modul: AccountingService::postEntry() HANYA commit bila
 * SUM(debit) == SUM(kredit) per entry (double-entry).
 */
class AccountingServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AccountingService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('petugas');
        $this->actingAs($user, 'sanctum');

        $this->service = app(AccountingService::class);
    }

    /** (a) create/store berhasil: jurnal balance ter-posting dengan baris yang benar. */
    public function test_post_entry_berhasil_untuk_jurnal_balance(): void
    {
        $kas = Account::factory()->create(['code' => '1-1000', 'type' => Account::TYPE_ASSET]);
        $pendapatan = Account::factory()->create(['code' => '4-1000', 'type' => Account::TYPE_REVENUE]);

        $entry = $this->service->postEntry([
            ['account_id' => $kas->id, 'debit' => 150000, 'kredit' => 0],
            ['account_id' => $pendapatan->id, 'debit' => 0, 'kredit' => 150000],
        ], description: 'Test posting');

        $this->assertDatabaseHas('journal_entries', ['id' => $entry->id, 'description' => 'Test posting']);
        $this->assertSame(2, JournalEntryLine::query()->where('entry_id', $entry->id)->count());
        $this->assertSame('150000.00', (string) $entry->lines->firstWhere('account_id', $kas->id)->debit);
        $this->assertSame('150000.00', (string) $entry->lines->firstWhere('account_id', $pendapatan->id)->kredit);
    }

    /** (b) gerbang bisnis: entry TIDAK balance harus ditolak, tidak ada baris yang ditulis. */
    public function test_post_entry_ditolak_bila_debit_kredit_tidak_balance(): void
    {
        $kas = Account::factory()->create();
        $pendapatan = Account::factory()->create();

        try {
            $this->service->postEntry([
                ['account_id' => $kas->id, 'debit' => 100000, 'kredit' => 0],
                ['account_id' => $pendapatan->id, 'debit' => 0, 'kredit' => 90000],
            ]);
            $this->fail('Harusnya ditolak karena tidak balance.');
        } catch (HttpException $e) {
            $this->assertSame(422, $e->getStatusCode());
        }

        $this->assertDatabaseCount('journal_entries', 0);
        $this->assertDatabaseCount('journal_entry_lines', 0);
    }

    /** (b) gerbang bisnis: minimal 2 baris per entry. */
    public function test_post_entry_ditolak_bila_kurang_dari_dua_baris(): void
    {
        $kas = Account::factory()->create();

        $this->assertThrows(
            fn () => $this->service->postEntry([
                ['account_id' => $kas->id, 'debit' => 100000, 'kredit' => 0],
            ]),
            HttpException::class,
        );
    }
}
