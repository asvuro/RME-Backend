<?php

namespace Modules\LayananImagingOrder\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\LayananImagingOrder\Http\Requests\StoreImagingOrderRequest;
use Modules\LayananImagingOrder\Http\Requests\UpdateImagingOrderRequest;
use Modules\LayananImagingOrder\Http\Resources\ImagingOrderResource;
use Modules\LayananImagingOrder\Models\ImagingOrder;
use Modules\LayananImagingOrder\Services\ImagingOrderService;

/**
 * Controller order imaging. Semua penulisan status lewat ImagingOrderService —
 * controller tidak pernah memanggil Model::create()/update() untuk aksi yang
 * punya gerbang (pola anti-bypass proyek ini; lihat VisitController/VisitService).
 */
class ImagingOrderController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = ImagingOrder::query();

        if ($request->filled('visit_id')) {
            $query->where('visit_id', $request->integer('visit_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('modality')) {
            $query->where('modality', $request->input('modality'));
        }

        return ImagingOrderResource::collection(
            $query->latest('id')->paginate($request->integer('per_page', 15)),
        );
    }

    public function store(StoreImagingOrderRequest $request, ImagingOrderService $service): JsonResponse
    {
        // Gerbang kunjungan aktif + penetapan status awal ada di service.
        $order = $service->create($request->validated());

        return (new ImagingOrderResource($order))->response()->setStatusCode(201);
    }

    public function show(ImagingOrder $imaging_order): ImagingOrderResource
    {
        return new ImagingOrderResource($imaging_order);
    }

    public function update(
        UpdateImagingOrderRequest $request,
        ImagingOrder $imaging_order,
        ImagingOrderService $service,
    ): ImagingOrderResource {
        return new ImagingOrderResource($service->updateDetails($imaging_order, $request->validated()));
    }

    /** Gerbang penjadwalan (termasuk jadwal ulang). */
    public function schedule(Request $request, ImagingOrder $imaging_order, ImagingOrderService $service): ImagingOrderResource
    {
        $data = $request->validate([
            'scheduled_at' => ['required', 'date'],
        ]);

        return new ImagingOrderResource($service->schedule($imaging_order, $data['scheduled_at']));
    }

    /** Gerbang pembatalan (soft-cancel; tanpa hard delete demi jejak audit). */
    public function cancel(ImagingOrder $imaging_order, ImagingOrderService $service): ImagingOrderResource
    {
        return new ImagingOrderResource($service->cancel($imaging_order));
    }
}
