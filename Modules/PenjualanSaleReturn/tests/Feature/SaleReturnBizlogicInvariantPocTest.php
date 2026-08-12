<?php

namespace Modules\PenjualanSaleReturn\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\PenjualanSale\Models\Sale;
use Modules\PenjualanSaleReturn\Models\SaleReturn;
use Tests\TestCase;

/**
 * POC-P7 regression: returns must be backed by what was actually sold.
 *
 * The endpoint previously accepted a free-form refund_amount (even 0) with
 * zero linkage to sale_items. It now requires items[] lines referencing
 * sale items of the sale, derives refund_amount = quantity x sold unit
 * price server-side, caps per-item returned quantity across stacked
 * returns, and rejects zero-value returns.
 */
class SaleReturnBizlogicInvariantPocTest extends TestCase
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
     * A return line must reference a real sold item; amounts are derived
     * server-side, so the client cannot type an arbitrary value.
     */
    public function test_return_requires_sold_item_and_derives_amount(): void
    {
        $this->actingUser();

        $sale = Sale::factory()->create([
            'total_amount' => '750000.00',
            'status' => 'completed',
        ]);
        $saleItem = \Modules\PenjualanSaleItem\Models\SaleItem::factory()->create([
            'sale_id' => $sale->id,
            'quantity' => 2,
            'unit_price' => '250000.00',
            'subtotal' => '500000.00',
        ]);

        // Unknown / foreign sale item id.
        $this->postJson('/api/v1/sale-returns', [
            'sale_id' => $sale->id,
            'items' => [['sale_item_id' => $saleItem->id + 999999, 'quantity' => 1]],
        ])->assertStatus(422);

        // Valid line: refund is derived, not client-typed.
        $response = $this->postJson('/api/v1/sale-returns', [
            'sale_id' => $sale->id,
            'reason' => 'One unit came back',
            'items' => [['sale_item_id' => $saleItem->id, 'quantity' => 1]],
        ])->assertCreated();

        $this->assertSame('250000.00', $response->json('data.refund_amount'));
        $this->assertSame(1, SaleReturn::query()->where('sale_id', $sale->id)->count());
    }

    /**
     * A return against a sale that has no sold items at all is rejected.
     */
    public function test_return_against_sale_without_items_is_rejected(): void
    {
        $this->actingUser();

        $sale = Sale::factory()->create([
            'total_amount' => '750000.00',
            'status' => 'completed',
        ]);

        $this->postJson('/api/v1/sale-returns', [
            'sale_id' => $sale->id,
            'items' => [['sale_item_id' => 424242, 'quantity' => 2]],
        ])->assertStatus(422);

        $this->assertDatabaseCount('sale_returns', 0);
    }

    /**
     * Boundary variant: a ZERO-refund row can no longer be created because
     * every accepted line moves the sold value back into the ledger.
     */
    public function test_zero_value_return_is_rejected(): void
    {
        $this->actingUser();

        $sale = Sale::factory()->create(['total_amount' => '100000.00']);
        $saleItem = \Modules\PenjualanSaleItem\Models\SaleItem::factory()->create([
            'sale_id' => $sale->id,
            'quantity' => 1,
            'unit_price' => '0.00',
            'subtotal' => '0.00',
        ]);

        $this->postJson('/api/v1/sale-returns', [
            'sale_id' => $sale->id,
            'items' => [['sale_item_id' => $saleItem->id, 'quantity' => 1]],
        ])->assertStatus(422);

        $this->assertDatabaseCount('sale_returns', 0);
    }
}