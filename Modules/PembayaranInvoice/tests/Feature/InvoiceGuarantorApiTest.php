<?php

namespace Modules\PembayaranInvoice\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\PembayaranInvoice\Models\Invoice;
use Modules\PembayaranInvoice\Services\InvoiceService;
use Modules\PembayaranInvoiceItem\Models\InvoiceItem;
use Modules\PendaftaranGuarantor\Models\Guarantor;
use Modules\PendaftaranVisit\Models\Visit;
use Tests\TestCase;

/**
 * API distribusi penjamin + lock kasir (port REST pembayaran simgos2).
 * Baca/lampir: semua pengguna terautentikasi; pembukaan kunci: role:admin.
 */
class InvoiceGuarantorApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }

    private function actingUser(?string $role = 'petugas'): User
    {
        $user = User::factory()->create();
        if ($role !== null) {
            $user->assignRole($role);
        }
        $this->actingAs($user, 'sanctum');

        return $user;
    }

    public function test_tamu_ditolak_401(): void
    {
        $invoice = Invoice::factory()->create();

        $this->getJson("/api/v1/invoices/{$invoice->id}/guarantors")->assertUnauthorized();
    }

    public function test_store_lampiran_201_dengan_sequence_pertama(): void
    {
        $this->actingUser();
        $invoice = Invoice::factory()->create();
        $guarantor = Guarantor::factory()->bpjs()->create(['registration_id' => $invoice->visit->registration_id]);

        $response = $this->postJson("/api/v1/invoices/{$invoice->id}/guarantors", [
            'guarantor_id' => $guarantor->id,
        ]);

        // Lampiran baru + redistribute segera: BPJS menanggung penuh.
        $response->assertCreated();
        $response->assertJsonFragment(['guarantor_id' => $guarantor->id, 'sequence' => 1]);
        $this->assertSame((string) $invoice->refresh()->total_amount, (string) $invoice->coveredAmount());
    }

    public function test_store_lampiran_ganda_mengembalikan_baris_sama_bukan_duplikat(): void
    {
        $this->actingUser();
        $invoice = Invoice::factory()->create();
        $guarantor = Guarantor::factory()->bpjs()->create(['registration_id' => $invoice->visit->registration_id]);

        $this->postJson("/api/v1/invoices/{$invoice->id}/guarantors", ['guarantor_id' => $guarantor->id]);
        $second = $this->postJson("/api/v1/invoices/{$invoice->id}/guarantors", ['guarantor_id' => $guarantor->id]);

        // Ala storePenjaminTagihan idempoten: 200 dengan baris yang sama.
        $second->assertOk();
        $this->assertSame(1, $invoice->guarantorAttachments()->count());
    }

    public function test_coverage_setelah_posting_item_dan_redistribute(): void
    {
        $service = app(InvoiceService::class);
        $this->actingUser();

        // End-to-end ala storeRincianTagihan → prosesDistribusiTarif:
        // posting item, lampir penjamin, recompute, baca ringkasan.
        $visit = Visit::factory()->create();
        $invoice = $service->ensureForVisit($visit->id);
        $bpjs = Guarantor::factory()->bpjs()->create(['registration_id' => $invoice->visit->registration_id]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => 'Konsultasi poliklinik',
            'category' => 'konsultasi',
            'quantity' => 1,
            'unit_price' => '150000.00',
            'subtotal' => '150000.00',
        ]);
        $service->recalculate($invoice);

        $this->postJson("/api/v1/invoices/{$invoice->id}/redistribute");

        $coverage = $this->getJson("/api/v1/invoices/{$invoice->id}/coverage");
        $coverage->assertOk();
        $coverage->assertJsonFragment([
            'total' => '150000.00',
            'covered' => '150000.00',
            'patient_share' => '0.00',
        ]);
        $this->assertSame($bpjs->id, (int) $invoice->guarantorAttachments()->first()?->guarantor_id);
    }

    public function test_lock_kasir_lalu_unlock_hanya_admin(): void
    {
        $admin = $this->actingUser('admin');
        $invoice = Invoice::factory()->create();

        $lock = $this->actingAs($admin, 'sanctum')->postJson("/api/v1/invoices/{$invoice->id}/lock");
        $lock->assertOk();
        $this->assertTrue($invoice->refresh()->is_locked);

        // Distribusi dibekukan saat terkunci.
        $guarantor = Guarantor::factory()->bpjs()->create(['registration_id' => $invoice->visit->registration_id]);
        $this->actingAs($admin, 'sanctum')
            ->postJson("/api/v1/invoices/{$invoice->id}/guarantors", ['guarantor_id' => $guarantor->id])
            ->assertStatus(422);

        // Non-admin tidak bisa membuka kembali.
        $petugas = User::factory()->create();
        $this->actingAs($petugas, 'sanctum')
            ->postJson("/api/v1/invoices/{$invoice->id}/unlock")
            ->assertForbidden();
        $this->assertTrue($invoice->refresh()->is_locked);

        // Admin membuka kembali.
        $this->actingAs($admin, 'sanctum')
            ->postJson("/api/v1/invoices/{$invoice->id}/unlock")
            ->assertOk();
        $this->assertFalse($invoice->refresh()->is_locked);
    }

    public function test_index_daftar_lampiran_terurut_sequence(): void
    {
        $this->actingUser();
        $invoice = Invoice::factory()->create();
        $registrationId = $invoice->visit->registration_id;

        $selfPay = Guarantor::factory()->create(['registration_id' => $registrationId]);
        $insurance = Guarantor::factory()->create(['registration_id' => $registrationId, 'payer_type' => 'insurance']);

        $service = app(InvoiceService::class);
        $service->attachGuarantor($invoice, $selfPay->id);
        $service->attachGuarantor($invoice, $insurance->id);

        $response = $this->getJson("/api/v1/invoices/{$invoice->id}/guarantors");

        $response->assertOk();
        $data = $response->json('data');
        $this->assertCount(2, $data);
        $this->assertSame([$selfPay->id, $insurance->id], array_column($data, 'guarantor_id'));
    }
}
