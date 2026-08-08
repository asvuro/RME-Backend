<?php

namespace Modules\PembayaranInvoiceItem\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\PembayaranInvoice\Models\Invoice;
use Modules\PembayaranInvoiceItem\Models\InvoiceItem;
use Tests\TestCase;

class InvoiceItemControllerTest extends TestCase
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

    public function test_it_adds_item_and_recalculates_invoice_total(): void
    {
        $this->actingUser();
        $invoice = Invoice::factory()->create();

        $this->postJson('/api/v1/invoice-items', [
            'invoice_id' => $invoice->id,
            'description' => 'Konsultasi Dokter',
            'quantity' => 2,
            'unit_price' => 50000,
        ])->assertCreated()->assertJsonPath('data.subtotal', '100000.00');

        $this->assertEquals(100000, $invoice->fresh()->total_amount);
    }

    public function test_it_cannot_add_item_to_locked_invoice(): void
    {
        $this->actingUser();
        $invoice = Invoice::factory()->locked()->create();

        $this->postJson('/api/v1/invoice-items', [
            'invoice_id' => $invoice->id,
            'description' => 'Obat',
            'quantity' => 1,
            'unit_price' => 10000,
        ])->assertStatus(422);
    }

    public function test_it_removes_item_and_recalculates_total(): void
    {
        $this->actingUser();
        $invoice = Invoice::factory()->create();
        $item = InvoiceItem::factory()->create(['invoice_id' => $invoice->id, 'quantity' => 1, 'unit_price' => 75000, 'subtotal' => 75000]);
        $invoice->recalculateTotals();

        $this->deleteJson("/api/v1/invoice-items/{$item->id}")->assertStatus(204);

        $this->assertEquals(0, $invoice->fresh()->total_amount);
    }

    public function test_petugas_cannot_change_unit_price(): void
    {
        $this->actingUser();
        $invoice = Invoice::factory()->create();
        $item = InvoiceItem::factory()->create(['invoice_id' => $invoice->id, 'unit_price' => 50000, 'subtotal' => 50000]);

        $this->putJson("/api/v1/invoice-items/{$item->id}", ['unit_price' => 0])
            ->assertStatus(403);

        $this->assertEquals(50000, $item->fresh()->unit_price);
    }

    public function test_admin_can_change_unit_price(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $this->actingAs($user, 'sanctum');

        $invoice = Invoice::factory()->create();
        $item = InvoiceItem::factory()->create(['invoice_id' => $invoice->id, 'quantity' => 1, 'unit_price' => 50000, 'subtotal' => 50000]);

        $this->putJson("/api/v1/invoice-items/{$item->id}", ['unit_price' => 40000])
            ->assertOk();

        $this->assertEquals(40000, $item->fresh()->unit_price);
    }
}
