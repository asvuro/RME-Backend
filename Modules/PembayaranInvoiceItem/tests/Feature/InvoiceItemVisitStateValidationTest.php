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
 * Independent validation of the invoice-items write endpoints against the
 * visit state machine. Every action under test is driven through the public
 * HTTP API as an authenticated petugas.
 */
class InvoiceItemVisitStateValidationTest extends TestCase
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

    public function test_val_p2a_post_item_on_discharged_visit_open_invoice_succeeds(): void
    {
        $this->actingUser();

        $visit = Visit::factory()->discharged()->create();
        $invoice = $this->openInvoiceFor($visit);

        // Setelah perbaikan: ditolak -- kunjungan sudah pulang.
        $response = $this->postJson('/api/v1/invoice-items', [
            'invoice_id' => $invoice->id,
            'description' => 'Tindakan setelah pasien pulang',
            'quantity' => 1,
            'unit_price' => 250000,
        ]);

        $response->assertStatus(422);

        $this->assertSame(0, $invoice->items()->count());
        $invoice->refresh();
        $this->assertEquals('0.00', (string) $invoice->total_amount,
            'no billing line may be posted to a closed encounter');
        $this->assertNotNull($visit->fresh()->discharged_at);
    }

    public function test_val_p2b_delete_item_on_cancelled_visit_open_invoice_succeeds(): void
    {
        $this->actingUser();

        $visit = Visit::factory()->create(['status' => 'cancelled']);
        $invoice = $this->openInvoiceFor($visit);
        $item = InvoiceItem::factory()->create([
            'invoice_id' => $invoice->id,
            'quantity' => 1,
            'unit_price' => 100000,
            'subtotal' => 100000,
        ]);
        $invoice->recalculateTotals();

        // Setelah perbaikan: penghapusan baris tagihan pada kunjungan batal ditolak.
        $this->deleteJson("/api/v1/invoice-items/{$item->id}")->assertStatus(422);

        $this->assertSame(1, $invoice->items()->count());
        $this->assertSame('cancelled', $visit->fresh()->status);
        $this->assertEquals('100000.00', (string) $invoice->fresh()->total_amount,
            'recorded charge of a voided encounter stays intact');
    }

    public function test_val_p2c_inflate_quantity_on_discharged_visit_invoice_succeeds(): void
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

        // Setelah perbaikan: inflasi nilai pada kunjungan pulang ditolak.
        $this->putJson("/api/v1/invoice-items/{$item->id}", ['quantity' => 99])
            ->assertStatus(422);

        $item->refresh();
        $this->assertSame(1, $item->quantity);
        $this->assertEquals('100000.00', (string) $invoice->fresh()->total_amount,
            'charge value on a closed encounter stays intact');
    }
}