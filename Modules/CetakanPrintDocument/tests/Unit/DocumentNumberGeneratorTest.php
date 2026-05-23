<?php

namespace Modules\CetakanPrintDocument\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\CetakanPrintDocument\Models\PrintDocument;
use Tests\TestCase;

/**
 * Nomor seri harian ala generator.generateIdKarcis simgos2:
 * {PREFIX}-{YYMMDD}-{seq4} berurut per jenis dokumen.
 */
class DocumentNumberGeneratorTest extends TestCase
{
    use RefreshDatabase;

    public function test_format_nomor_pertama_per_jenis(): void
    {
        $stamp = now()->format('ymd');

        $this->assertSame("RCPT-{$stamp}-0001", PrintDocument::generateDocumentNumber(PrintDocument::TYPE_RECEIPT));
        $this->assertSame("KRCS-{$stamp}-0001", PrintDocument::generateDocumentNumber(PrintDocument::TYPE_KARCIS));
        $this->assertSame("WSTB-{$stamp}-0001", PrintDocument::generateDocumentNumber(PrintDocument::TYPE_WRISTBAND));
        $this->assertSame("TRCR-{$stamp}-0001", PrintDocument::generateDocumentNumber(PrintDocument::TYPE_TRACER));
    }

    public function test_sequence_naik_per_jenis_dalam_hari(): void
    {
        $stamp = now()->format('ymd');

        PrintDocument::query()->create([
            'document_type' => PrintDocument::TYPE_RECEIPT,
            'ref_type' => 'payments',
            'ref_id' => 1,
            'document_number' => "RCPT-{$stamp}-0001",
            'issued_at' => now(),
        ]);

        $this->assertSame("RCPT-{$stamp}-0002", PrintDocument::generateDocumentNumber(PrintDocument::TYPE_RECEIPT));

        // Jenis lain tak terpengaruh urutan RCPT.
        $this->assertSame("KRCS-{$stamp}-0001", PrintDocument::generateDocumentNumber(PrintDocument::TYPE_KARCIS));

        // Generator murni membaca nomor terbesar — setelah 0002 benar-benar terbit,
        // urutan berikutnya lanjut dari situ.
        PrintDocument::query()->create([
            'document_type' => PrintDocument::TYPE_RECEIPT,
            'ref_type' => 'payments',
            'ref_id' => 2,
            'document_number' => "RCPT-{$stamp}-0002",
            'issued_at' => now(),
        ]);

        $this->assertSame("RCPT-{$stamp}-0003", PrintDocument::generateDocumentNumber(PrintDocument::TYPE_RECEIPT));
    }
}
