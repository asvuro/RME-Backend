<?php

namespace Modules\PembayaranInvoiceCancellation\Http\Controllers;

use App\Events\InvoiceLocked;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\PembayaranInvoice\Models\Invoice;
use Modules\PembayaranInvoiceCancellation\Http\Requests\StoreInvoiceCancellationRequest;
use Modules\PembayaranInvoiceCancellation\Http\Resources\InvoiceCancellationResource;
use Modules\PembayaranInvoiceCancellation\Models\InvoiceCancellation;

class InvoiceCancellationController extends Controller
{
    public function index(Request $request)
    {
        $query = InvoiceCancellation::query();

        if ($request->filled('invoice_id')) {
            $query->where('invoice_id', $request->integer('invoice_id'));
        }

        return InvoiceCancellationResource::collection($query->latest('cancelled_at')->paginate($request->integer('per_page', 15)));
    }

    /**
     * Cancellation is a legal/financial record - append-only, no update/delete.
     * Locks the invoice and marks it cancelled.
     *
     * Gerbang (temuan pentest vuln-0017): satu invoice hanya boleh punya SATU
     * baris pembatalan (dicek+dikunci dalam transaksi yang sama supaya request
     * paralel tidak lolos berdua), dan invoice yang statusnya 'paid' tidak
     * boleh dibatalkan langsung lewat jalur ini -- pembayaran yang sudah
     * masuk butuh alur refund/pembatalan pembayaran formal, bukan status
     * flip mentah yang meninggalkan payment row menggantung tanpa reversal.
     */
    public function store(StoreInvoiceCancellationRequest $request)
    {
        $data = $request->validated();
        $data['cancelled_at'] = now();
        $data['cancelled_by'] = $request->user()->id;

        $cancellation = DB::transaction(function () use ($data) {
            $invoice = Invoice::query()->whereKey($data['invoice_id'])->lockForUpdate()->firstOrFail();

            abort_if(
                InvoiceCancellation::where('invoice_id', $invoice->id)->exists(),
                422,
                'Invoice ini sudah pernah dibatalkan.'
            );

            abort_if(
                $invoice->status === 'paid',
                422,
                'Invoice yang sudah dibayar tidak dapat langsung dibatalkan; gunakan alur refund/pembatalan pembayaran.'
            );

            $cancellation = InvoiceCancellation::create($data);
            $invoice->update(['status' => 'cancelled', 'is_locked' => true]);

            return $cancellation;
        });

        InvoiceLocked::dispatch(Invoice::findOrFail($data['invoice_id']));

        return (new InvoiceCancellationResource($cancellation))->response()->setStatusCode(201);
    }

    public function show(InvoiceCancellation $invoice_cancellation): InvoiceCancellationResource
    {
        return new InvoiceCancellationResource($invoice_cancellation);
    }
}
