<?php

namespace Modules\PembayaranInvoice\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\PembayaranInvoice\Http\Requests\StoreInvoiceRequest;
use Modules\PembayaranInvoice\Http\Requests\UpdateInvoiceRequest;
use Modules\PembayaranInvoice\Http\Resources\InvoiceResource;
use Modules\PembayaranInvoice\Models\Invoice;
use Modules\PembayaranInvoice\Services\InvoiceService;

class InvoiceController extends Controller
{
    public function __construct(private readonly InvoiceService $invoiceService)
    {
    }

    public function index(Request $request)
    {
        $query = Invoice::query();

        if ($request->filled('visit_id')) {
            $query->where('visit_id', $request->integer('visit_id'));
        }

        return InvoiceResource::collection($query->latest('invoice_date')->paginate($request->integer('per_page', 15)));
    }

    public function store(StoreInvoiceRequest $request)
    {
        $data = $request->validated();

        $attributes = collect($data)
            ->only(['invoice_number', 'invoice_date', 'rounding_adjustment'])
            ->filter(fn ($value) => $value !== null)
            ->all();

        $invoice = $this->invoiceService->createForVisit(
            (int) $data['visit_id'],
            $attributes,
            $request->user()->id,
        );

        return (new InvoiceResource($invoice))->response()->setStatusCode(201);
    }

    public function show(Invoice $invoice): InvoiceResource
    {
        return new InvoiceResource($invoice->load('items', 'payments'));
    }

    public function update(UpdateInvoiceRequest $request, Invoice $invoice): InvoiceResource
    {
        $invoice = $this->invoiceService->updateInvoice($invoice, $request->validated());

        return new InvoiceResource($invoice);
    }

    public function destroy(Invoice $invoice)
    {
        $this->invoiceService->deleteInvoice($invoice);

        return response()->json(null, 204);
    }
}
