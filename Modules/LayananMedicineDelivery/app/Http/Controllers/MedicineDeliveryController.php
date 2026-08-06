<?php

namespace Modules\LayananMedicineDelivery\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Auth\Models\User;
use Modules\LayananMedicineDelivery\Http\Requests\StoreMedicineDeliveryRequest;
use Modules\LayananMedicineDelivery\Http\Requests\UpdateMedicineDeliveryRequest;
use Modules\LayananMedicineDelivery\Http\Resources\MedicineDeliveryResource;
use Modules\LayananMedicineDelivery\Models\MedicineDelivery;
use Modules\LayananMedicineDelivery\Services\MedicineDeliveryService;

/**
 * Semua mutasi state (status, kurir, waktu) lewat MedicineDeliveryService -
 * controller tidak pernah menulis model langsung (anti-bypass gerbang).
 */
class MedicineDeliveryController extends Controller
{
    public function __construct(protected MedicineDeliveryService $service) {}

    public function index(Request $request)
    {
        $query = MedicineDelivery::query();

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('courier_employee_id')) {
            $query->where('courier_employee_id', $request->integer('courier_employee_id'));
        }

        return MedicineDeliveryResource::collection(
            $query->orderBy('id', 'desc')->paginate($request->integer('per_page', 15)),
        );
    }

    /**
     * Gerbang dispense-ready + aturan satu-pengantaran-per-dispense ada di
     * service; input klien tidak pernah menentukan status awal.
     */
    public function store(StoreMedicineDeliveryRequest $request)
    {
        $delivery = $this->service->create($request->validated());

        return (new MedicineDeliveryResource($delivery))->response()->setStatusCode(201);
    }

    public function show(MedicineDelivery $delivery): MedicineDeliveryResource
    {
        return new MedicineDeliveryResource($delivery);
    }

    /** Edit bebas cuma untuk alamat tujuan; status/kurir lewat gerbang khusus. */
    public function update(UpdateMedicineDeliveryRequest $request, MedicineDelivery $delivery): MedicineDeliveryResource
    {
        $delivery = $this->service->updateAddress($delivery, $request->validated());

        return new MedicineDeliveryResource($delivery);
    }

    public function destroy(MedicineDelivery $delivery): JsonResponse
    {
        $this->service->deleteDelivery($delivery);

        return response()->json(null, 204);
    }

    /** Gerbang penugasan kurir: sekalian menandai paket berangkat (dikirim). */
    public function assignCourier(Request $request, MedicineDelivery $delivery): MedicineDeliveryResource
    {
        $data = $request->validate([
            'courier_employee_id' => ['required', 'integer', 'exists:employees,id'],
        ]);

        $delivery = $this->service->assignCourier($delivery, (int) $data['courier_employee_id']);

        return new MedicineDeliveryResource($delivery);
    }

    /** Gerbang serah terima: wajib punya kurir dan sedang dikirim (cek di service). */
    public function markDelivered(Request $request, MedicineDelivery $delivery): MedicineDeliveryResource
    {
        /** @var User $user */
        $user = $request->user();

        $delivery = $this->service->markDelivered($delivery, $user);

        return new MedicineDeliveryResource($delivery);
    }
}
