<?php

namespace Modules\PembayaranInvoice\Tests\Unit;

use App\Modules\Contracts\BillingGate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\PembayaranInvoice\Services\InvoiceService;
use Modules\PembayaranInvoice\Models\Invoice;
use Modules\PendaftaranVisit\Models\Visit;
use Tests\TestCase;

class InvoiceServiceGateTest extends TestCase
{
    use RefreshDatabase;

    protected InvoiceService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(InvoiceService::class);
    }

    protected function createInvoice(array $attributes = []): Invoice
    {
        return Invoice::factory()->create($attributes);
    }

    public function test_lock_mengunci_dan_unlock_membuka_invoice(): void
    {
        $invoice = $this->createInvoice(['is_locked' => false]);

        $this->service->lock($invoice->id);
        $this->assertTrue($invoice->refresh()->is_locked);

        $this->service->unlock($invoice->id);
        $this->assertFalse($invoice->refresh()->is_locked);
    }

    public function test_is_visit_locked_true_bila_ada_invoice_terkunci(): void
    {
        $visit = Visit::factory()->create();
        $this->createInvoice(['visit_id' => $visit->id, 'is_locked' => true]);

        $this->assertTrue($this->service->isVisitLocked($visit->id));
    }

    public function test_is_visit_locked_false_bila_semua_invoice_terbuka(): void
    {
        $visit = Visit::factory()->create();
        $this->createInvoice(['visit_id' => $visit->id, 'is_locked' => false]);

        $this->assertFalse($this->service->isVisitLocked($visit->id));
    }

    public function test_is_visit_locked_false_bila_kunjungan_tanpa_invoice(): void
    {
        $visit = Visit::factory()->create();

        $this->assertFalse($this->service->isVisitLocked($visit->id));
    }

    public function test_service_terikat_sebagai_billing_gate(): void
    {
        $this->assertInstanceOf(InvoiceService::class, app(BillingGate::class));
    }
}
