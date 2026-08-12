<?php

namespace Modules\PembayaranInvoice\Tests\Feature;

use Modules\PembayaranInvoice\Models\Invoice;
use Modules\PembayaranInvoiceItem\Models\InvoiceItem;

class InvoiceItemStub
{
    public static function for(Invoice $invoice, float $unitPrice): InvoiceItem
    {
        $item = InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => 'Tindakan',
            'quantity' => 1,
            'unit_price' => $unitPrice,
            'subtotal' => $unitPrice,
        ]);

        $invoice->recalculateTotals();

        return $item;
    }
}