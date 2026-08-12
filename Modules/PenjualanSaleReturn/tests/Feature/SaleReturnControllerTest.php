<?php

namespace Modules\PenjualanSaleReturn\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\PenjualanSale\Models\Sale;
use Modules\PenjualanSaleItem\Models\SaleItem;
use Modules\PenjualanSaleReturn\Models\SaleReturn;
use Tests\TestCase;

class SaleReturnControllerTest extends TestCase
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

    public function test_it_records_a_sale_return(): void
    {
        $this->actingUser();
        $sale = Sale::factory()->create();

        $saleItem = SaleItem::factory()->create([
            'sale_id' => $sale->id,
            'quantity' => 3,
            'unit_price' => '10000.00',
            'subtotal' => '30000.00',
        ]);

        $response = $this->postJson('/api/v1/sale-returns', [
            'sale_id' => $sale->id,
            'reason' => 'Barang rusak',
            'items' => [['sale_item_id' => $saleItem->id, 'quantity' => 2]],
        ]);

        $response->assertCreated();
        $response->assertJsonPath('data.sale_id', $sale->id);
        $response->assertJsonPath('data.refund_amount', '20000.00');
        $this->assertDatabaseHas('sale_return_items', [
            'sale_return_id' => $response->json('data.id'),
            'sale_item_id' => $saleItem->id,
            'quantity' => 2,
            'refunded_amount' => '20000.00',
        ]);
    }

    public function test_it_rejects_refund_exceeding_sale_total(): void
    {
        $this->actingUser();
        $sale = Sale::factory()->create(['total_amount' => 50]);
        $saleItem = SaleItem::factory()->create([
            'sale_id' => $sale->id,
            'quantity' => 1,
            'unit_price' => '50.00',
            'subtotal' => '50.00',
        ]);

        $this->postJson('/api/v1/sale-returns', [
            'sale_id' => $sale->id,
            'items' => [['sale_item_id' => $saleItem->id, 'quantity' => 100]],
        ])->assertStatus(422);

        $this->assertDatabaseCount('sale_returns', 0);
    }

    public function test_it_rejects_stacked_returns_exceeding_sale_total(): void
    {
        $this->actingUser();
        $sale = Sale::factory()->create(['total_amount' => 50]);
        $saleItem = SaleItem::factory()->create([
            'sale_id' => $sale->id,
            'quantity' => 2,
            'unit_price' => '25.00',
            'subtotal' => '50.00',
        ]);

        $this->postJson('/api/v1/sale-returns', [
            'sale_id' => $sale->id,
            'items' => [['sale_item_id' => $saleItem->id, 'quantity' => 1]],
        ])->assertCreated();

        $this->postJson('/api/v1/sale-returns', [
            'sale_id' => $sale->id,
            'items' => [['sale_item_id' => $saleItem->id, 'quantity' => 1]],
        ])->assertCreated();

        $this->postJson('/api/v1/sale-returns', [
            'sale_id' => $sale->id,
            'items' => [['sale_item_id' => $saleItem->id, 'quantity' => 1]],
        ])->assertStatus(422);

        $this->assertDatabaseCount('sale_returns', 2);
    }

    public function test_it_lists_returns_filtered_by_sale(): void
    {
        $this->actingUser();
        $sale = Sale::factory()->create();
        SaleReturn::factory()->count(2)->create(['sale_id' => $sale->id]);
        SaleReturn::factory()->create();

        $response = $this->getJson("/api/v1/sale-returns?sale_id={$sale->id}");

        $response->assertOk()->assertJsonCount(2, 'data');
    }

    public function test_it_has_no_update_or_delete_route(): void
    {
        $this->actingUser();
        $return = SaleReturn::factory()->create();

        $this->putJson("/api/v1/sale-returns/{$return->id}", ['refund_amount' => 1])->assertStatus(405);
        $this->deleteJson("/api/v1/sale-returns/{$return->id}")->assertStatus(405);
    }

    public function test_guest_cannot_access_sale_returns(): void
    {
        $this->getJson('/api/v1/sale-returns')->assertStatus(401);
    }
}
