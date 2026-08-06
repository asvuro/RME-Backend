<?php

namespace Modules\LayananTelemedicineSession\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\LayananTelemedicineSession\Http\Requests\CompleteTelemedicineSessionRequest;
use Modules\LayananTelemedicineSession\Http\Requests\StoreTelemedicineSessionRequest;
use Modules\LayananTelemedicineSession\Http\Requests\UpdateTelemedicineSessionRequest;
use Modules\LayananTelemedicineSession\Http\Resources\TelemedicineSessionResource;
use Modules\LayananTelemedicineSession\Models\TelemedicineSession;
use Modules\LayananTelemedicineSession\Services\TelemedicineSessionService;

class TelemedicineSessionController extends Controller
{
    public function __construct(protected TelemedicineSessionService $service) {}

    public function index(Request $request)
    {
        $sessions = TelemedicineSession::query()
            ->when($request->query('visit_id'), fn ($query, $visitId) => $query->where('visit_id', (int) $visitId))
            ->orderBy('id', 'desc')
            ->paginate($request->integer('per_page', 15));

        return TelemedicineSessionResource::collection($sessions);
    }

    /**
     * Membuat sesi selalu lewat TelemedicineSessionService::schedule():
     * status dan session_url bukan input bebas klien, melainkan hasil gerbang
     * bisnis (kunjungan hidup + tidak ada sesi aktif ganda).
     */
    public function store(StoreTelemedicineSessionRequest $request)
    {
        $session = $this->service->schedule($request->validated());

        return (new TelemedicineSessionResource($session))->response()->setStatusCode(201);
    }

    public function show(TelemedicineSession $session): TelemedicineSessionResource
    {
        return new TelemedicineSessionResource($session);
    }

    /**
     * Dua wajah PUT/PATCH: pembatalan (status=cancelled, tanpa field lain) atau
     * sunting atribut biasa. Transisi lain TIDAK lewat sini — start/complete
     * wajib endpoint khususnya agar gerbang urutan status ditegakkan service.
     */
    public function update(UpdateTelemedicineSessionRequest $request, TelemedicineSession $session)
    {
        $data = $request->validated();

        if (array_key_exists('status', $data)) {
            abort_unless(
                $data['status'] === TelemedicineSession::STATUS_CANCELLED && count($data) === 1,
                422,
                'Transisi status lewat endpoint ini hanya pembatalan (status=cancelled), tanpa field lain.',
            );

            return new TelemedicineSessionResource($this->service->cancel($session));
        }

        return new TelemedicineSessionResource($this->service->updateDetails($session, $data));
    }

    public function destroy(TelemedicineSession $session)
    {
        $this->service->delete($session);

        return response()->noContent();
    }

    /** Gerbang urutan: scheduled -> ongoing (POST .../{session}/start). */
    public function start(TelemedicineSession $session): TelemedicineSessionResource
    {
        return new TelemedicineSessionResource($this->service->start($session));
    }

    /** Gerbang urutan: ongoing -> completed (POST .../{session}/complete). */
    public function complete(CompleteTelemedicineSessionRequest $request, TelemedicineSession $session): TelemedicineSessionResource
    {
        return new TelemedicineSessionResource(
            $this->service->complete($session, $request->validated('consultation_notes'))
        );
    }
}
