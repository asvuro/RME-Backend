<?php

namespace Modules\PembayaranInvoice\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\PembayaranInvoice\Models\Invoice;
use Modules\PembayaranInvoice\Services\InvoiceService;
use Modules\PembayaranInvoiceGuarantor\Http\Resources\InvoiceGuarantorResource;

/**
 * Port REST distribusi penjamin simgos2: lampiran penjamin ke tagihan
 * (storePenjaminTagihan), recompute (reProsesDistribusiTarif) dan ringkasan
 * tanggungan (getTotalPenjaminTagihan). Gerbang locked hidup di service.
 */
class InvoiceGuarantorController extends Controller
{
    public function __construct(protected InvoiceService $service) {}

    public function index(Invoice $invoice): AnonymousResourceCollection
    {
        return InvoiceGuarantorResource::collection(
            $invoice->guarantorAttachments()->get(),
        );
    }

    public function store(Request $request, Invoice $invoice): JsonResponse
    {
        $validated = $request->validate([
            'guarantor_id' => ['required', 'integer', 'exists:guarantors,id'],
            // Kelas klaim (port KELAS_KLAIM); default mengikuti guarantor.
            'room_class_id' => ['nullable', 'integer', 'exists:room_classes,id'],
        ]);

        $attachment = $this->service->attachGuarantor(
            $invoice,
            $validated['guarantor_id'],
            $validated['room_class_id'] ?? null,
        );

        // Lampiran baru belum membawa nominal - recompute segera agar coverage konsisten.
        $this->service->redistribute($invoice);

        return (new InvoiceGuarantorResource($attachment->refresh()))
            ->response()
            ->setStatusCode($attachment->wasRecentlyCreated ? 201 : 200);
    }

    public function redistribute(Invoice $invoice): JsonResponse
    {
        $this->service->redistribute($invoice);

        return response()->json(['data' => $this->service->coverage($invoice)]);
    }

    public function coverage(Invoice $invoice): JsonResponse
    {
        return response()->json(['data' => $this->service->coverage($invoice)]);
    }

    /** Kasir menutup tagihan (port STATUS=2 final). */
    public function lock(Invoice $invoice): JsonResponse
    {
        $this->service->lock($invoice->id);

        return response()->json(['data' => ['id' => $invoice->id, 'is_locked' => true]]);
    }

    /** Pembukaan kembali untuk koreksi - hanya admin (rute role:admin). */
    public function unlock(Invoice $invoice): JsonResponse
    {
        $this->service->unlock($invoice->id);

        return response()->json(['data' => ['id' => $invoice->id, 'is_locked' => false]]);
    }
}
