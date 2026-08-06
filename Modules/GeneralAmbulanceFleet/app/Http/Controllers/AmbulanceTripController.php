<?php

namespace Modules\GeneralAmbulanceFleet\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\GeneralAmbulanceFleet\Http\Requests\CompleteAmbulanceTripRequest;
use Modules\GeneralAmbulanceFleet\Http\Requests\StoreAmbulanceTripRequest;
use Modules\GeneralAmbulanceFleet\Http\Requests\UpdateAmbulanceTripRequest;
use Modules\GeneralAmbulanceFleet\Models\AmbulanceTrip;
use Modules\GeneralAmbulanceFleet\Services\AmbulanceTripService;

/**
 * CRUD trip ambulans. Semua aksi yang menyentuh status (mulai / selesai)
 * dipaksa lewat AmbulanceTripService karena mengubah status ambulans juga -
 * controller tidak boleh menulis Model::create()/update() langsung untuk
 * transisi ini. destroy() tidak disediakan: trip adalah jejak operasional.
 */
class AmbulanceTripController extends Controller
{
    public function __construct(protected AmbulanceTripService $service) {}

    public function index(Request $request)
    {
        $query = AmbulanceTrip::query()->with(['ambulance', 'driver']);

        if ($request->filled('ambulance_id')) {
            $query->where('ambulance_id', $request->integer('ambulance_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('purpose')) {
            $query->where('purpose', $request->string('purpose'));
        }

        return $query->orderByDesc('departed_at')->paginate($request->integer('per_page', 15));
    }

    /** Mulai trip: gerbang 422 bila ambulans sedang in_use/maintenance. */
    public function store(StoreAmbulanceTripRequest $request)
    {
        $trip = $this->service->start($request->validated());

        return response()->json($trip, 201);
    }

    public function show(AmbulanceTrip $trip): AmbulanceTrip
    {
        return $trip->loadMissing(['ambulance', 'patient', 'driver']);
    }

    public function update(UpdateAmbulanceTripRequest $request, AmbulanceTrip $trip): AmbulanceTrip
    {
        return $this->service->updateDetails($trip, $request->validated());
    }

    /**
     * Selesaikan trip: returned_at tercatat dan ambulans kembali available.
     * Ditolak 422 bila trip sudah completed/cancelled.
     */
    public function complete(CompleteAmbulanceTripRequest $request, AmbulanceTrip $trip): AmbulanceTrip
    {
        return $this->service->complete($trip, $request->input('returned_at'));
    }
}
