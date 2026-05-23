<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\CetakanPrintDocument\Models\PrintDocument;

/**
 * Efek samping non-kritis setelah dokumen cetak terbit (jangkar audit #12).
 * Tanpa listener = no-op; idempotensi tetap di PrintDocumentService::issue().
 */
class PrintDocumentIssued
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly PrintDocument $document) {}
}
