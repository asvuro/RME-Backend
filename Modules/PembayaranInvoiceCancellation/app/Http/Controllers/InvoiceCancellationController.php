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
    public function __construct(private readonly \Modules\PembayaranInvoice\Services\InvoiceService $invoiceService)
    {
    }

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
     * Transisi lock + gerbang duplikat/paid + dispatch audit ada di
     * InvoiceService::cancel() -- jalur kanonik tunggal untuk membatalkan
     * invoice (regresi vuln-0017: dulu controller menulis status invoice
     * langsung lalu memicu InvoiceLocked di luar service, sehingga semantik
     * lock dan jejak audit menyimpang dari jalur resmi).
     */
    public function store(StoreInvoiceCancellationRequest $request)
    {
        $data = $request->validated();
        $data['cancelled_at'] = now();
        $data['cancelled_by'] = $request->user()->id;

        $cancellation = DB::transaction(function () use ($data) {
            $this->invoiceService->cancel((int) $data['invoice_id']);

            $cancellation = InvoiceCancellation::create($data);

            return $cancellation;
        });

        return (new InvoiceCancellationResource($cancellation))->response()->setStatusCode(201);
    }

    public function show(InvoiceCancellation $invoice_cancellation): InvoiceCancellationResource
    {
        return new InvoiceCancellationResource($invoice_cancellation);
    }
}
