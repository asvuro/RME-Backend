<?php

namespace Modules\FinanceGeneralLedger\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\FinanceGeneralLedger\Models\Account;
use Modules\FinanceGeneralLedger\Services\AccountingService;
use Tests\TestCase;

/** (c) list/index endpoint read-only, role:admin. */
class JournalEntryControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_admin_can_list_journal_entries(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin, 'sanctum');

        $kas = Account::factory()->create();
        $pendapatan = Account::factory()->create();

        app(AccountingService::class)->postEntry([
            ['account_id' => $kas->id, 'debit' => 50000, 'kredit' => 0],
            ['account_id' => $pendapatan->id, 'debit' => 0, 'kredit' => 50000],
        ]);

        $this->getJson('/api/v1/journal-entries')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_petugas_forbidden_from_listing_journal_entries(): void
    {
        $petugas = User::factory()->create();
        $petugas->assignRole('petugas');
        $this->actingAs($petugas, 'sanctum');

        $this->getJson('/api/v1/journal-entries')->assertStatus(403);
    }

    public function test_admin_can_list_accounts(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin, 'sanctum');

        Account::factory()->count(3)->create();

        $this->getJson('/api/v1/accounts')
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }
}
