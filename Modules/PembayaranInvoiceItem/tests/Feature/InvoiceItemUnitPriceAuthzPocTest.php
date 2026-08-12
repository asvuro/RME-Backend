<?php

namespace Modules\PembayaranInvoiceItem\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralService\Models\Service;
use Modules\GeneralServiceTariff\Models\ServiceTariff;
use Modules\PembayaranInvoice\Models\Invoice;
use Tests\TestCase;

/**
 * POC: sibling-write-path gap for the admin-only unit_price rule.
 *
 * Commit cff79389 gates unit_price behind role:admin on UPDATE only. The
 * STORE path accepts unit_price from any authenticated petugas, so the
 * price control is trivially bypassed by creating a new item instead of
 * editing an existing one.
 */
class InvoiceItemUnitPriceAuthzPocTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }

    private function actingPetugas(): User
    {
        $user = User::factory()->create();
        $user->assignRole('petugas');
        $this->actingAs($user, 'sanctum');

        return $user;
    }

    private function actingAdmin(): User
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $this->actingAs($user, 'sanctum');

        return $user;
    }

    public function test_petugas_cannot_supply_unit_price_on_store(): void
    {
        $this->actingPetugas();
        $invoice = Invoice::factory()->create();

        $response = $this->postJson('/api/v1/invoice-items', [
            'invoice_id' => $invoice->id,
            'description' => 'Item harga nol',
            'quantity' => 1,
            'unit_price' => 0,
        ]);

        if ($response->status() === 201) {
            $invoice->refresh();
            fwrite(STDERR, sprintf(
                "[POC-A] petugas unit_price=0 accepted; stored total_amount=%s\n",
                $invoice->total_amount,
            ));
            $this->fail('[POC-A] petugas set arbitrary unit_price on invoice-item store');
        }

        // Lapis validasi menolak lebih dulu (422); gerbang 403 identik dengan
        // update() tetap terpasang di controller sebagai pertahanan kedua.
        $response->assertStatus(422);
        $this->assertDatabaseCount('invoice_items', 0);
    }

    public function test_petugas_can_add_item_priced_from_service_tariff(): void
    {
        $this->actingPetugas();
        $invoice = Invoice::factory()->create();
        $service = Service::factory()->create();
        ServiceTariff::factory()->create(['service_id' => $service->id, 'price' => 75000]);

        $this->postJson('/api/v1/invoice-items', [
            'invoice_id' => $invoice->id,
            'service_id' => $service->id,
            'description' => 'Konsultasi katalog',
            'quantity' => 2,
        ])->assertCreated()->assertJsonPath('data.subtotal', '150000.00');

        $this->assertEquals(150000, $invoice->fresh()->total_amount);
    }

    public function test_admin_can_still_supply_unit_price_on_store(): void
    {
        $this->actingAdmin();
        $invoice = Invoice::factory()->create();

        $this->postJson('/api/v1/invoice-items', [
            'invoice_id' => $invoice->id,
            'description' => 'Item harga khusus',
            'quantity' => 1,
            'unit_price' => 12345,
        ])->assertCreated()->assertJsonPath('data.unit_price', '12345.00');
    }
}