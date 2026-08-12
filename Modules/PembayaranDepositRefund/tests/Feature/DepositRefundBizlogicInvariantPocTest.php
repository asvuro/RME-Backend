<?php

namespace Modules\PembayaranDepositRefund\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\PembayaranDeposit\Models\Deposit;
use Modules\PembayaranDepositRefund\Models\DepositRefund;
use Tests\TestCase;

/**
 * POC-P5 regression: a partial (or zero) refund must NOT close the deposit;
 * status flips to 'refunded' only once cumulative refunds reach the amount.
 *
 * DepositRefundController::store previously flipped status to 'refunded'
 * after ANY accepted refund, stranding the un-refunded remainder behind the
 * status !== 'held' gate. The fix keeps 'held' until fully refunded and adds
 * an over-refund regression case.
 */
class DepositRefundBizlogicInvariantPocTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }

    private function actingUser(): User
    {
        $user = User::factory()->create();
        $user->assignRole('petugas');
        $this->actingAs($user, 'sanctum');

        return $user;
    }

    /**
     * A partial refund keeps the remainder payable through the API.
     */
    public function test_partial_refund_keeps_remainder_refundable(): void
    {
        $this->actingUser();

        $deposit = Deposit::factory()->create([
            'amount' => '1000000.00',
            'status' => 'held',
        ]);

        // Patient only wants 400k back now.
        $this->postJson('/api/v1/deposit-refunds', [
            'deposit_id' => $deposit->id,
            'refunded_amount' => 400000,
        ])->assertCreated();

        $deposit->refresh();
        $this->assertSame('held', $deposit->status,
            'partial refund must not close the deposit');

        // The remaining 600k is still payable through the API.
        $this->postJson('/api/v1/deposit-refunds', [
            'deposit_id' => $deposit->id,
            'refunded_amount' => 600000,
        ])->assertCreated();

        $this->assertSame('refunded', $deposit->fresh()->status,
            'cumulative refunds reached the deposit amount, so it closes now');
        $this->assertEquals(1000000.0, (float) DepositRefund::query()
            ->where('deposit_id', $deposit->id)
            ->sum('refunded_amount'));
    }

    /**
     * Over-refunding beyond the deposit amount stays rejected.
     */
    public function test_refund_exceeding_deposit_amount_is_rejected(): void
    {
        $this->actingUser();

        $deposit = Deposit::factory()->create([
            'amount' => '1000000.00',
            'status' => 'held',
        ]);

        $this->postJson('/api/v1/deposit-refunds', [
            'deposit_id' => $deposit->id,
            'refunded_amount' => 1000000.01,
        ])->assertStatus(422);

        $this->assertDatabaseCount('deposit_refunds', 0);
        $this->assertSame('held', $deposit->fresh()->status);
    }

    /**
     * A full refund in one shot still closes the deposit immediately.
     */
    public function test_full_refund_closes_deposit(): void
    {
        $this->actingUser();

        $deposit = Deposit::factory()->create([
            'amount' => '500000.00',
            'status' => 'held',
        ]);

        $this->postJson('/api/v1/deposit-refunds', [
            'deposit_id' => $deposit->id,
            'refunded_amount' => 500000,
        ])->assertCreated();

        $this->assertSame('refunded', $deposit->fresh()->status);

        $this->postJson('/api/v1/deposit-refunds', [
            'deposit_id' => $deposit->id,
            'refunded_amount' => 1,
        ])->assertStatus(422);

        $this->assertSame(1, DepositRefund::query()->where('deposit_id', $deposit->id)->count());
    }

    /**
     * Regression for the zero-amount variant: it must no longer burn the
     * deposit status.
     */
    public function test_zero_amount_refund_leaves_deposit_usable(): void
    {
        $this->actingUser();

        $deposit = Deposit::factory()->create([
            'amount' => '500000.00',
            'status' => 'held',
        ]);

        $this->postJson('/api/v1/deposit-refunds', [
            'deposit_id' => $deposit->id,
            'refunded_amount' => 0,
        ])->assertCreated();

        $this->assertSame('held', $deposit->fresh()->status,
            'a zero-value refund row must not strand the deposit');

        $this->postJson('/api/v1/deposit-refunds', [
            'deposit_id' => $deposit->id,
            'refunded_amount' => 500000,
        ])->assertCreated();

        $this->assertSame('refunded', $deposit->fresh()->status);
        $this->assertDatabaseCount('deposit_refunds', 2);
    }
}