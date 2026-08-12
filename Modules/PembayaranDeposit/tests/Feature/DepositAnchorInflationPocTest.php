<?php

namespace Modules\PembayaranDeposit\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\PendaftaranVisit\Models\Visit;
use Tests\TestCase;

/**
 * POC: refund caps are anchored to deposit.amount, but any petugas can set
 * that anchor arbitrarily at deposit creation, then refund against it. The
 * cumulative-sum cap introduced in cff79389 holds only for honest anchors.
 */
class DepositAnchorInflationPocTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }

    private function actingPetugas(): User
    {
        $user = User::factory()->create();
        $user->assignRole('petugas');
        $this->actingAs($user, 'sanctum');

        return $user;
    }

    public function test_petugas_cannot_inflate_deposit_anchor_past_ceiling(): void
    {
        $this->actingPetugas();
        $visit = Visit::factory()->create();

        $response = $this->postJson('/api/v1/deposits', [
            'visit_id' => $visit->id,
            'amount' => 999999000,
        ]);

        if ($response->status() === 201) {
            $refund = $this->postJson('/api/v1/deposit-refunds', [
                'deposit_id' => $response->json('data.id'),
                'refunded_amount' => 999999000,
            ]);
            fwrite(STDERR, sprintf(
                "[POC-D] anchor inflation accepted id=%s; refund against it -> HTTP %s (cumulative refunded 999999000)\n",
                $response->json('data.id'),
                $refund->status(),
            ));
            $this->fail('[POC-D] petugas inflated deposit anchor then refunded against it');
        }

        $response->assertStatus(422);
    }

    public function test_direct_refunded_status_flip_without_accounting_is_rejected(): void
    {
        $this->actingPetugas();
        $deposit = \Modules\PembayaranDeposit\Models\Deposit::factory()->create(['status' => 'held']);

        $this->putJson("/api/v1/deposits/{$deposit->id}", ['status' => 'refunded'])
            ->assertStatus(422);

        $this->assertSame('held', $deposit->fresh()->status);
    }

    public function test_ordinary_deposit_refund_flow_still_works(): void
    {
        $this->actingPetugas();
        $visit = Visit::factory()->create();

        $deposit = $this->postJson('/api/v1/deposits', [
            'visit_id' => $visit->id,
            'amount' => 500000,
        ])->assertCreated()->assertJsonPath('data.status', 'held')->json('data.id');

        $this->postJson('/api/v1/deposit-refunds', [
            'deposit_id' => $deposit,
            'refunded_amount' => 500000,
        ])->assertCreated();

        $this->assertSame('refunded', \Modules\PembayaranDeposit\Models\Deposit::find($deposit)->status);
    }
}