<?php

namespace Modules\AuditInfectionSurveillance\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\AuditInfectionSurveillance\Http\Requests\StoreDeviceDayRequest;
use Modules\AuditInfectionSurveillance\Http\Requests\UpdateDeviceDayRequest;
use Modules\AuditInfectionSurveillance\Models\DeviceDay;
use Modules\AuditInfectionSurveillance\Services\SurveillanceService;

class DeviceDayController extends Controller
{
    public function __construct(protected SurveillanceService $surveillanceService) {}

    public function index(Request $request)
    {
        $query = DeviceDay::query()->with('visit');

        if ($request->filled('visit_id')) {
            $query->where('visit_id', $request->integer('visit_id'));
        }

        if ($request->filled('device_type')) {
            $query->where('device_type', $request->input('device_type'));
        }

        return $query->latest('inserted_at')->paginate($request->integer('per_page', 15));
    }

    public function store(StoreDeviceDayRequest $request): JsonResponse
    {
        // Gerbang rentang pasang/lepas ada di SurveillanceService::createDeviceDay().
        $deviceDay = $this->surveillanceService->createDeviceDay($request->validated());

        return response()->json($deviceDay, 201);
    }

    public function show(DeviceDay $deviceDay): DeviceDay
    {
        return $deviceDay->load('visit');
    }

    public function update(UpdateDeviceDayRequest $request, DeviceDay $deviceDay): DeviceDay
    {
        // Rentang gabungan nilai lama+baru tetap dicek gerbang service.
        return $this->surveillanceService->updateDeviceDay($deviceDay, $request->validated());
    }

    public function destroy(DeviceDay $deviceDay): JsonResponse
    {
        $this->surveillanceService->deleteDeviceDay($deviceDay);

        return response()->json(null, 204);
    }
}
