<?php

namespace Modules\AuditActivityLog\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\AuditActivityLog\Models\ActivityLog;
use Modules\CetakanPrintDocument\Models\PrintDocument;
use Modules\InventoryWardStockTransaction\Models\WardStockTransaction;
use Modules\PembayaranPayment\Models\Payment;
use Modules\PendaftaranVisit\Models\Visit;
use Tests\TestCase;

/**
 * Trait Auditable pada model mesin state (#7–#11): Visit, Invoice, Bed, Payment.
 * Ditambah WardStockTransaction + PrintDocument (menutup gap audit write-path
 * yang tercatat di AUTH_MATRIX Section 5).
 */
class AuditableTraitTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_mencatat_keadaan_sesudah(): void
    {
        $visit = Visit::factory()->create(['status' => 'active']);

        $row = ActivityLog::query()->where('object', 'visits')->firstOrFail();
        $this->assertSame(ActivityLog::ACTION_CREATED, $row->action);
        $this->assertSame((string) $visit->id, $row->ref);
        $this->assertNull($row->before);
        $this->assertSame('active', $row->after['status']);
    }

    public function test_update_mencatat_diff_sebelum_sesudah(): void
    {
        $visit = Visit::factory()->create(['status' => 'active']);
        ActivityLog::query()->where('object', 'visits')->delete();

        $visit->update(['status' => 'discharged', 'final_outcome' => 'sembuh']);

        $row = ActivityLog::query()->where('object', 'visits')->firstOrFail();
        $this->assertSame(ActivityLog::ACTION_UPDATED, $row->action);
        $this->assertSame('active', $row->before['status']);
        $this->assertSame('discharged', $row->after['status']);
        $this->assertArrayNotHasKey('visit_number', $row->after); // hanya kolom berubah
        $this->assertArrayNotHasKey('updated_at', $row->after);
    }

    public function test_update_tanpa_perubahan_tidak_menulis(): void
    {
        $visit = Visit::factory()->create();

        ActivityLog::query()->delete();
        $visit->update(['status' => $visit->status]);

        $this->assertSame(0, ActivityLog::count());
    }

    public function test_delete_mencatat_keadaan_sebelum(): void
    {
        $visit = Visit::factory()->create(['final_outcome' => 'meninggal']);

        ActivityLog::query()->delete();
        $visit->delete();

        $row = ActivityLog::query()->where('object', 'visits')->firstOrFail();
        $this->assertSame(ActivityLog::ACTION_DELETED, $row->action);
        $this->assertSame('meninggal', $row->before['final_outcome']);
        $this->assertNull($row->after);
    }

    public function test_payment_create_dan_delete_tercatat(): void
    {
        $payment = Payment::factory()->create(['amount' => 150000]);

        $created = ActivityLog::query()->where('object', 'payments')->where('action', ActivityLog::ACTION_CREATED)->firstOrFail();
        $this->assertSame((string) $payment->id, $created->ref);
        $this->assertEquals(150000, $created->after['amount']);

        $payment->delete();

        $deleted = ActivityLog::query()->where('object', 'payments')->where('action', ActivityLog::ACTION_DELETED)->firstOrFail();
        $this->assertSame((string) $payment->id, $deleted->ref);
        $this->assertNull($deleted->after);
    }

    public function test_ward_stock_transaction_create_tercatat(): void
    {
        $trx = WardStockTransaction::factory()->create(['type' => 'out', 'quantity' => 5]);

        $row = ActivityLog::query()->where('object', 'ward_stock_transactions')->firstOrFail();
        $this->assertSame(ActivityLog::ACTION_CREATED, $row->action);
        $this->assertSame((string) $trx->id, $row->ref);
        $this->assertSame('out', $row->after['type']);
        $this->assertEquals(5, $row->after['quantity']);
    }

    public function test_print_document_create_tercatat_tanpa_payload(): void
    {
        $doc = PrintDocument::factory()->create([
            'document_type' => 'karcis',
            'payload' => ['nama_pasien' => 'Bukan Untuk Audit'],
        ]);

        $row = ActivityLog::query()->where('object', 'print_documents')->firstOrFail();
        $this->assertSame(ActivityLog::ACTION_CREATED, $row->action);
        $this->assertSame((string) $doc->id, $row->ref);
        $this->assertSame('karcis', $row->after['document_type']);
        $this->assertSame($doc->document_number, $row->after['document_number']);
        // Payload = snapshot identitas pasien; dilarang tersalin ke audit log.
        $this->assertArrayNotHasKey('payload', $row->after);
    }

    public function test_print_document_update_payload_tercatat_termask_bukan_lenyap(): void
    {
        $doc = PrintDocument::factory()->create(['payload' => ['nama_pasien' => 'Lama']]);
        ActivityLog::query()->delete();

        // Penerbitan ulang karcis: satu-satunya perubahan adalah payload.
        $doc->update(['payload' => ['nama_pasien' => 'Baru']]);

        $row = ActivityLog::query()->where('object', 'print_documents')->firstOrFail();
        $this->assertSame(ActivityLog::ACTION_UPDATED, $row->action);
        $this->assertSame('[hidden]', $row->after['payload']);
        $this->assertSame('[hidden]', $row->before['payload']);
    }
}
