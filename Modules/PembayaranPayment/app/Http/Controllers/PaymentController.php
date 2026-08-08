<?php

namespace Modules\PembayaranPayment\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\PembayaranInvoice\Models\Invoice;
use Modules\PembayaranInvoice\Services\InvoiceService;
use Modules\PembayaranPayment\Http\Requests\StorePaymentRequest;
use Modules\PembayaranPayment\Http\Resources\PaymentResource;
use Modules\PembayaranPayment\Models\Payment;

class PaymentController extends Controller
{
    public function __construct(protected InvoiceService $invoiceService) {}

    public function index(Request $request)
    {
        $query = Payment::query();

        if ($request->filled('invoice_id')) {
            $query->where('invoice_id', $request->integer('invoice_id'));
        }

        return PaymentResource::collection($query->latest('paid_at')->paginate($request->integer('per_page', 15)));
    }

    /**
     * Payments are financial records, not freely editable/deletable once made -
     * only index/store/show. Corrections belong in a reversal entry, not in scope yet.
     */
    public function store(StorePaymentRequest $request)
    {
        $data = $request->validated();
        $data['payment_number'] ??= Payment::generatePaymentNumber();
        $data['paid_at'] ??= now();
        $data['received_by'] = $request->user()->id;

        $payment = DB::transaction(function () use ($data) {
            $invoice = Invoice::query()->whereKey($data['invoice_id'])->lockForUpdate()->firstOrFail();

            abort_if($invoice->is_locked, 422, 'Tagihan ini sudah lunas dan dikunci.');
            abort_if($invoice->status === 'cancelled', 422, 'Tagihan ini sudah dibatalkan.');

            $alreadyPaid = (float) $invoice->payments()->sum('amount');
            $outstanding = (float) $invoice->total_amount - $alreadyPaid;

            abort_if(
                (float) $data['amount'] > $outstanding,
                422,
                'Jumlah pembayaran melebihi sisa tagihan.'
            );

            $payment = Payment::create($data);

            if ($alreadyPaid + (float) $data['amount'] >= (float) $invoice->total_amount) {
                $this->invoiceService->markPaid($invoice->id);
            }

            return $payment;
        });

        return (new PaymentResource($payment))->response()->setStatusCode(201);
    }

    public function show(Payment $payment): PaymentResource
    {
        return new PaymentResource($payment);
    }
}
