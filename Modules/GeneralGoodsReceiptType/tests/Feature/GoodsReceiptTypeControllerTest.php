<?php

namespace Modules\GeneralGoodsReceiptType\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralGoodsReceiptType\Models\GoodsReceiptType;
use Tests\TestCase;

class GoodsReceiptTypeControllerTest extends TestCase
{
    use RefreshDatabase;


    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }
    private function actingUser(): void
    {
        $user = User::factory()->create();
        $user->assignRole('petugas');
        $this->actingAs($user, 'sanctum');
    }

    public function test_it_lists_goods_receipt_type(): void
    {
        $this->actingUser();
        GoodsReceiptType::factory()->count(3)->create();

        $this->getJson('/api/v1/goods-receipt-types')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_goods_receipt_type(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/goods-receipt-types', ['name' => 'Contoh Jenispenerimaan', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Jenispenerimaan');

        $this->assertDatabaseHas('goods_receipt_types', ['name' => 'Contoh Jenispenerimaan']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        GoodsReceiptType::factory()->create(['name' => 'Contoh Jenispenerimaan']);

        $this->postJson('/api/v1/goods-receipt-types', ['name' => 'Contoh Jenispenerimaan'])->assertStatus(422);
    }

    public function test_it_deletes_goods_receipt_type(): void
    {
        $this->actingUser();
        $goodsReceiptType = GoodsReceiptType::factory()->create();

        $this->deleteJson("/api/v1/goods-receipt-types/{$goodsReceiptType->id}")->assertStatus(204);
        $this->assertDatabaseMissing('goods_receipt_types', ['id' => $goodsReceiptType->id]);
    }
}