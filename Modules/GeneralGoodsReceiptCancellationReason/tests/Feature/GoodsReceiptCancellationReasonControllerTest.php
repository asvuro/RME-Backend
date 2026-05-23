<?php

namespace Modules\GeneralGoodsReceiptCancellationReason\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralGoodsReceiptCancellationReason\Models\GoodsReceiptCancellationReason;
use Tests\TestCase;

class GoodsReceiptCancellationReasonControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_goods_receipt_cancellation_reason(): void
    {
        $this->actingUser();
        GoodsReceiptCancellationReason::factory()->count(3)->create();

        $this->getJson('/api/v1/goods-receipt-cancellation-reasons')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_goods_receipt_cancellation_reason(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/goods-receipt-cancellation-reasons', ['name' => 'Contoh Alasanpembatalanpenerimaanbarang', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Alasanpembatalanpenerimaanbarang');

        $this->assertDatabaseHas('goods_receipt_cancellation_reasons', ['name' => 'Contoh Alasanpembatalanpenerimaanbarang']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        GoodsReceiptCancellationReason::factory()->create(['name' => 'Contoh Alasanpembatalanpenerimaanbarang']);

        $this->postJson('/api/v1/goods-receipt-cancellation-reasons', ['name' => 'Contoh Alasanpembatalanpenerimaanbarang'])->assertStatus(422);
    }

    public function test_it_deletes_goods_receipt_cancellation_reason(): void
    {
        $this->actingUser();
        $record = GoodsReceiptCancellationReason::factory()->create();

        $this->deleteJson("/api/v1/goods-receipt-cancellation-reasons/{$record->id}")->assertStatus(204);
        $this->assertDatabaseMissing('goods_receipt_cancellation_reasons', ['id' => $record->id]);
    }
}