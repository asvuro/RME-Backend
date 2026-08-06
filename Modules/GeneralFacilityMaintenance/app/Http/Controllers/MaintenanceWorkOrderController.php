<?php

namespace Modules\GeneralFacilityMaintenance\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\GeneralFacilityMaintenance\Http\Requests\AssignMaintenanceWorkOrderRequest;
use Modules\GeneralFacilityMaintenance\Http\Requests\StoreMaintenanceWorkOrderRequest;
use Modules\GeneralFacilityMaintenance\Http\Requests\UpdateMaintenanceWorkOrderRequest;
use Modules\GeneralFacilityMaintenance\Models\MaintenanceWorkOrder;
use Modules\GeneralFacilityMaintenance\Services\MaintenanceWorkOrderService;

class MaintenanceWorkOrderController extends Controller
{
    public function __construct(protected MaintenanceWorkOrderService $service)
    {
    }

    public function index(Request $request)
    {
        $query = MaintenanceWorkOrder::query();

        if ($request->filled('asset_id')) {
            $query->where('asset_id', $request->integer('asset_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        return $query->latest('reported_at')->paginate($request->integer('per_page', 15));
    }

    // Membuat work order mengubah status asset (side effect) sehingga wajib
    // lewat service, bukan MaintenanceWorkOrder::create() langsung.
    public function store(StoreMaintenanceWorkOrderRequest $request)
    {
        $workOrder = $this->service->createWorkOrder($request->validated());

        return response()->json($workOrder->refresh(), 201);
    }

    public function show(MaintenanceWorkOrder $wo): MaintenanceWorkOrder
    {
        return $wo;
    }

    // Update biasa hanya menyentuh field deskriptif (lihat
    // UpdateMaintenanceWorkOrderRequest) — status/assigned_to/completed_at
    // tetap dijaga service assign()/complete().
    public function update(UpdateMaintenanceWorkOrderRequest $request, MaintenanceWorkOrder $wo): MaintenanceWorkOrder
    {
        $wo->update($request->validated());

        return $wo;
    }

    public function destroy(MaintenanceWorkOrder $wo)
    {
        $wo->delete();

        return response()->json(null, 204);
    }

    public function assign(AssignMaintenanceWorkOrderRequest $request, MaintenanceWorkOrder $wo)
    {
        $workOrder = $this->service->assign($wo->id, (int) $request->validated('assigned_to'));

        return response()->json($workOrder->refresh());
    }

    public function complete(Request $request, MaintenanceWorkOrder $wo)
    {
        $workOrder = $this->service->complete($wo->id);

        return response()->json($workOrder->refresh());
    }
}
