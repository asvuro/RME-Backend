<?php

namespace Modules\PenjualanSaleReturn\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\InventoryItem\Models\Item;
use Modules\PenjualanSale\Models\Sale;
use Modules\PenjualanSaleItem\Models\SaleItem;
use Modules\PenjualanSaleReturn\Models\SaleReturn;
use Tests\TestCase;

/**
 * Validation for POC-P7: a return must be backed by goods that were actually
 * sold, refund what those items were sold for (server-derived), stay within
 * the per-item sold value across stacked returns, and never be zero-value.
 */
class SaleReturnLinkedToSoldItemsValidationTest extends TestCase
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

    private function createSaleWithItems(): Sale
    {
        $sale = Sale::factory()->create([
            'total_amount' => '750000.00',
            'status' => 'completed',
        ]);

        SaleItem::factory()->create([
            'sale_id' => $sale->id,
            'item_id' => Item::factory(),
            'quantity' => 2,
            'unit_price' => '250000.00',
            'subtotal' => '500000.00',
        ]);
        SaleItem::factory()->create([
            'sale_id' => $sale->id,
            'item_id' => Item::factory(),
            'quantity' => 5,
            'unit_price' => '50000.00',
            'subtotal' => '250000.00',
        ]);

        return $sale;
    }

    public function test_full_value_return_without_goods_is_rejected(): void
    {
        $this->actingUser();

        // The POC-P7 payload: full-value amount typed manually, no items.
        $this->postJson('/api/v1/sale-returns', [
            'sale_id' => $this->createSaleWithItems()->id,
            'reason' => 'no items actually returned; amount typed manually',
            'refund_amount' => 750000,
        ])->assertStatus(422);

        $this->assertDatabaseCount('sale_returns', 0);
    }

    public function test_return_for_sale_with_no_items_is_rejected(): void
    {
        $this->actingUser();

        $sale = Sale::factory()->create([
            'total_amount' => '100000.00',
            'status' => 'completed',
        ]);

        $this->postJson('/api/v1/sale-returns', [
            'sale_id' => $sale->id,
            'reason' => 'nothing was ever sold on this sale',
            'refund_amount' => 50000,
        ])->assertStatus(422);

        $this->assertDatabaseCount('sale_returns', 0);
    }

    public function test_zero_refund_row_is_rejected(): void
    {
        $this->actingUser();

        $sale = $this->createSaleWithItems();

        $this->postJson('/api/v1/sale-returns', [
            'sale_id' => $sale->id,
            'refund_amount' => 0,
        ])->assertStatus(422);

        $this->assertDatabaseCount('sale_returns', 0);
    }

    public function test_item_linked_return_records_server_derived_amount(): void
    {
        $this->actingUser();

        $sale = $this->createSaleWithItems();

        // Client claims an inflated amount; server derives the real one.
        $response = $this->postJson('/api/v1/sale-returns', [
            'sale_id' => $sale->id,
            'items' => [['sale_item_id' => 999999999, 'quantity' => 2]],
        ]);

        $response->assertStatus(422);

        $response = $this->postJson('/api/v1/sale-returns', [
            'sale_id' => $sale->id,
            'reason' => 'One unit of the 250k item came back',
            'items' => [
                ['sale_item_id' => SaleItem::query()->where('sale_id', $sale->id)->first()->id, 'quantity' => 1],
            ],
        ])->assertCreated();

        $returnId = $response->json('data.id');
        $this->assertSame('250000.00', $response->json('data.refund_amount'),
            'refund must equal the sold value of the returned quantity');
        $this->assertDatabaseHas('sale_return_items', [
            'sale_return_id' => $returnId,
            'sale_item_id' => SaleItem::query()->where('sale_id', $sale->id)->first()->id,
            'quantity' => 1,
            'refunded_amount' => '250000.00',
        ]);
    }

    public function test_stacked_returns_cannot_exceed_sold_quantity_per_item(): void
    {
        $this->actingUser();

        $sale = $this->createSaleWithItems();
        $saleItemId = SaleItem::query()->where('sale_id', $sale->id)->orderBy('id')->first()->id;

        $this->postJson('/api/v1/sale-returns', [
            'sale_id' => $sale->id,
            'items' => [['sale_item_id' => $saleItemId, 'quantity' => 2]],
        ])->assertCreated();

        // One more unit than was ever sold for this line.
        $this->postJson('/api/v1/sale-returns', [
            'sale_id' => $sale->id,
            'items' => [['sale_item_id' => $saleItemId, 'quantity' => 1]],
        ])->assertStatus(422);

        $this->assertSame(1, SaleReturn::query()->count());
        $this->assertEquals(500000.0, (float) SaleReturn::query()->sum('refund_amount'));
    }
}