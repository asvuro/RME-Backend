<?php

namespace Modules\PembayaranInvoiceItem\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\PembayaranInvoice\Models\Invoice;
use Modules\PembayaranPayment\Models\Payment;
use Tests\TestCase;

/**
 * POC-P6 regression: numeric bounds on invoice item creation.
 *
 * quantity is capped at 9999 and unit_price at 999999999 so that a single
 * line can never exceed the decimal(15,2) columns (9999 x 999999999 =
 * 9,998,999,990,001 < 10^13). Subtotal math uses bcmath, exact to cents.
 */
class InvoiceItemNumericBoundaryPocTest extends TestCase
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
     * The former overflow payload must fail validation outright.
     */
    public function test_huge_quantity_price_payload_is_rejected(): void
    {
        $this->actingUser();

        $invoice = Invoice::factory()->create(['total_amount' => 0]);

        // 99999 x 9,999,999,999,999.00 used to overflow decimal(15,2).
        $this->postJson('/api/v1/invoice-items', [
            'invoice_id' => $invoice->id,
            'description' => 'Overflow probe',
            'quantity' => 99999,
            'unit_price' => 9999999999999,
        ])->assertStatus(422);

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

    /**
     * The worst case allowed by the new bounds stays exact: 9999 x
     * 999,999,999.00 = 9,998,999,990,001.00 inside decimal(15,2), computed
     * with bcmath so no float drift corrupts the stored total.
     */
    public function test_max_bounded_line_stores_exact_subtotal(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $user->assignRole('petugas');
        $this->actingAs($user, 'sanctum');

        $invoice = Invoice::factory()->create(['total_amount' => 0]);

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
}