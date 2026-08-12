<?php

namespace Modules\PembayaranInvoiceItem\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\PembayaranInvoice\Models\Invoice;
use Modules\PembayaranInvoiceItem\Models\InvoiceItem;
use Tests\TestCase;

/**
 * Validation for POC-P6: quantity/unit_price must be bounded so a single line
 * item can never overflow the decimal(15,2) columns, and subtotal math must be
 * exact (no float drift) within those bounds.
 */
class InvoiceItemNumericBoundsValidationTest extends TestCase
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

    public function test_unbounded_quantity_and_unit_price_are_rejected(): void
    {
        $this->actingUser();

        $invoice = Invoice::factory()->create(['total_amount' => 0]);

        // The POC-P6 payload (99999 x 9999999999999) must fail validation...
        $this->postJson('/api/v1/invoice-items', [
            'invoice_id' => $invoice->id,
            'description' => 'Overflow probe',
            'quantity' => 99999,
            'unit_price' => 9999999999999,
        ])->assertStatus(422);

        // ...as must each bound individually.
        $this->postJson('/api/v1/invoice-items', [
            'invoice_id' => $invoice->id,
            'description' => 'Quantity over cap',
            'quantity' => 100000,
            'unit_price' => 50000,
        ])->assertStatus(422);

        $this->postJson('/api/v1/invoice-items', [
            'invoice_id' => $invoice->id,
            'description' => 'Unit price over cap',
            'quantity' => 1,
            'unit_price' => 1000000000,
        ])->assertStatus(422);

        $this->assertSame(0, $invoice->items()->count());
        $this->assertEquals('0.00', (string) $invoice->fresh()->total_amount);
    }

    public function test_max_bounded_line_stores_exact_subtotal(): void
    {
        $user = $this->actingUser();
        $user->assignRole('admin');

        $invoice = Invoice::factory()->create(['total_amount' => 0]);

        // Worst case allowed by the new bounds: 9999 x 999,999,999.00 =
        // 9,998,999,990,001.00 - fits inside decimal(15,2).
        $response = $this->postJson('/api/v1/invoice-items', [
            'invoice_id' => $invoice->id,
            'description' => 'Worst-case bounded line',
            'quantity' => 9999,
            'unit_price' => 999999999,
        ]);

        $response->assertCreated();

        $item = $invoice->items()->first();
        $this->assertSame('9998999990001.00', (string) $item->subtotal);
        $this->assertEquals('9998999990001.00', (string) $invoice->fresh()->total_amount);
    }

    public function test_update_bounds_apply_to_quantity(): void
    {
        $this->actingUser();

        $invoice = Invoice::factory()->create();
        $item = InvoiceItem::factory()->create([
            'invoice_id' => $invoice->id,
            'quantity' => 1,
            'unit_price' => 50000,
            'subtotal' => 50000,
        ]);

        $this->putJson("/api/v1/invoice-items/{$item->id}", ['quantity' => 100000])
            ->assertStatus(422);

        $this->assertSame(1, $item->fresh()->quantity);
    }
}