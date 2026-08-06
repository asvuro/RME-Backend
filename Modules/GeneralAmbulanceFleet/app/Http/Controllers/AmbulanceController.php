<?php

namespace Modules\GeneralAmbulanceFleet\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\GeneralAmbulanceFleet\Http\Requests\StoreAmbulanceRequest;
use Modules\GeneralAmbulanceFleet\Http\Requests\UpdateAmbulanceRequest;
use Modules\GeneralAmbulanceFleet\Models\Ambulance;
use Modules\GeneralAmbulanceFleet\Services\AmbulanceService;

/**
 * CRUD armada ambulans. destroy() sengaja tidak disediakan: FK trip memakai
 * cascadeOnDelete, jadi menghapus ambulans akan ikut menghapus riwayat trip.
 * Armada yang pensiun cukup diberi status maintenance + catatan operasional.
 */
class AmbulanceController extends Controller
{
    public function __construct(protected AmbulanceService $service) {}

    public function index(Request $request)
    {
        $query = Ambulance::query();

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        return $query->orderBy('vehicle_code')->paginate($request->integer('per_page', 15));
    }

    public function store(StoreAmbulanceRequest $request)
    {
        $ambulance = $this->service->register($request->validated());

        return response()->json($ambulance, 201);
    }

    public function show(Ambulance $ambulance): Ambulance
    {
        return $ambulance;
    }

    public function update(UpdateAmbulanceRequest $request, Ambulance $ambulance): Ambulance
    {
        // Status tidak pernah ditulis langsung di sini: transisi legal
        // dinilai gerbang di service (in_use hanya lewat mulai/selesai trip).
        return $this->service->updateDetails($ambulance, $request->validated());
    }
}
