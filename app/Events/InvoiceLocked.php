<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\PembayaranInvoice\Models\Invoice;

/**
 * Kasir menutup/mengunci tagihan (port STATUS=2 final simgos2). Efek samping
 * non-kritis setelah commit (audit trail #12 nanti memakainya). Tanpa listener
 * = no-op; alur transaksional tetap di InvoiceService.
 */
class InvoiceLocked
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly Invoice $invoice) {}
}
