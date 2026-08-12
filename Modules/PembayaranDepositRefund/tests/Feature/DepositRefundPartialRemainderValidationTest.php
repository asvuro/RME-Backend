<?php

namespace Modules\PembayaranDepositRefund\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\PembayaranDeposit\Models\Deposit;
use Modules\PembayaranDepositRefund\Models\DepositRefund;
use Tests\TestCase;

/**
 * Validation for POC-P5: the remainder of a partially refunded deposit must
 * stay reachable through the API. Status may flip to 'refunded' only once the
 * cumulative refunds reach the deposited amount.
 */
class DepositRefundPartialRemainderValidationTest extends TestCase
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

    public function test_partial_refund_keeps_remainder_refundable(): void
    {
        $this->actingUser();

        $deposit = Deposit::factory()->create([
            'amount' => '1000000.00',
            'status' => 'held',
        ]);

        $this->postJson('/api/v1/deposit-refunds', [
            'deposit_id' => $deposit->id,
            'refunded_amount' => 400000,
        ])->assertCreated();

        $this->assertSame('held', $deposit->fresh()->status,
            'partial refund must not close the deposit');

        // The remaining 600k must still be payable out through the API.
        $this->postJson('/api/v1/deposit-refunds', [
            'deposit_id' => $deposit->id,
            'refunded_amount' => 600000,
        ])->assertCreated();

        $this->assertSame('refunded', $deposit->fresh()->status,
            'cumulative refunds reached the deposit amount, so it closes now');
        $this->assertEquals(1000000.0, (float) DepositRefund::query()->sum('refunded_amount'));
    }

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
    }
}