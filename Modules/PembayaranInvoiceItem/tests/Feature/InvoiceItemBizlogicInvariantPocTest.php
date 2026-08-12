<?php

namespace Modules\PembayaranInvoiceItem\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\PembayaranInvoice\Models\Invoice;
use Modules\PembayaranInvoiceItem\Models\InvoiceItem;
use Modules\PendaftaranVisit\Models\Visit;
use Tests\TestCase;

/**
 * P2 regression contracts: invoice-items CRUD enforces the owning visit's
 * lifecycle via VisitGate -- no posting, mutation or removal of billing lines
 * on DISCHARGED or CANCELLED encounters, fail-closed with 422.
 */
class InvoiceItemBizlogicInvariantPocTest extends TestCase
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

    private function openInvoiceFor(Visit $visit): Invoice
    {
        return Invoice::factory()->create(['visit_id' => $visit->id]);
    }

    /**
     * POST /invoice-items onto the still-open invoice of a discharged visit
     * is rejected: no new charge after the encounter has ended.
     */
    public function test_poc_p2a_post_item_on_discharged_visit_open_invoice_succeeds(): void
    {
        $this->actingUser();

        $visit = Visit::factory()->discharged()->create();
        $invoice = $this->openInvoiceFor($visit);

        $this->postJson('/api/v1/invoice-items', [
            'invoice_id' => $invoice->id,
            'description' => 'Tindakan setelah pasien pulang',
            'quantity' => 1,
            'unit_price' => 250000,
        ])->assertStatus(422);

        $this->assertSame(0, $invoice->items()->count());
        $this->assertEquals('0.00', (string) $invoice->fresh()->total_amount,
            'no billing line may be posted to a closed encounter');
    }

    /**
     * DELETE /invoice-items/{id} on the open invoice of a CANCELLED visit is
     * rejected: recorded charges of a voided encounter cannot be removed.
     */
    public function test_poc_p2b_delete_item_on_cancelled_visit_open_invoice_succeeds(): void
    {
        $this->actingUser();

        $visit = Visit::factory()->create(['status' => 'cancelled']);
        $invoice = $this->openInvoiceFor($visit);

        $item = InvoiceItem::factory()->create([
            'invoice_id' => $invoice->id,
            'quantity' => 2,
            'unit_price' => 50000,
            'subtotal' => 100000,
        ]);
        $invoice->recalculateTotals();

        $this->deleteJson("/api/v1/invoice-items/{$item->id}")->assertStatus(422);

        $this->assertSame(1, $invoice->items()->count(), 'charge row of a cancelled encounter survives');
        $this->assertEquals('100000.00', (string) $invoice->fresh()->total_amount);
    }

    /**
     * PUT /invoice-items/{id} on the open invoice of a discharged visit is
     * rejected: quantity inflation (or any change) cannot touch a closed
     * encounter's billing line.
     */
    public function test_poc_p2c_inflate_quantity_on_discharged_visit_invoice_succeeds(): void
    {
        $this->actingUser();

        $visit = Visit::factory()->discharged()->create();
        $invoice = $this->openInvoiceFor($visit);

        $item = InvoiceItem::factory()->create([
            'invoice_id' => $invoice->id,
            'quantity' => 1,
            'unit_price' => 100000,
            'subtotal' => 100000,
        ]);
        $invoice->recalculateTotals();

        $this->putJson("/api/v1/invoice-items/{$item->id}", ['quantity' => 99])
            ->assertStatus(422);

        $this->assertSame(1, (int) $item->fresh()->quantity);
        $this->assertEquals('100000.00', (string) $invoice->fresh()->total_amount,
            'closed encounter billing stays untouched');
    }

    /**
     * Contrast case proving the gap is about the missing visit gate, not the
     * lock flag: the same request against a LOCKED invoice is rejected.
     * (Kept as-is: this contract predates the visit gate fix.)
     */
    public function test_control_locked_invoice_still_rejects_new_item(): void
    {
        $this->actingUser();

        $visit = Visit::factory()->discharged()->create();
        $invoice = Invoice::factory()->locked()->create(['visit_id' => $visit->id]);

        $this->postJson('/api/v1/invoice-items', [
            'invoice_id' => $invoice->id,
            'description' => 'Obat',
            'quantity' => 1,
            'unit_price' => 10000,
        ])->assertStatus(422);
    }
}