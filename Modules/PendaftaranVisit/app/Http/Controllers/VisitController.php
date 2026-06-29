<?php

namespace Modules\PendaftaranVisit\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\PendaftaranVisit\Services\VisitService;
use Modules\PendaftaranVisit\Http\Requests\StoreVisitRequest;
use Modules\PendaftaranVisit\Http\Requests\UpdateVisitRequest;
use Modules\PendaftaranVisit\Http\Resources\VisitResource;
use Modules\PendaftaranVisit\Models\Visit;

class VisitController extends Controller
{
    public function index(Request $request)
    {
        $query = Visit::query();

        if ($request->filled('registration_id')) {
            $query->where('registration_id', $request->integer('registration_id'));
        }

        return VisitResource::collection($query->latest('admitted_at')->paginate($request->integer('per_page', 15)));
    }

    public function store(StoreVisitRequest $request, VisitService $service)
    {
        // Gerbang admission ala simgos2 ada di VisitService::admit().
        $visit = $service->admit($request->validated(), $request->user());

        return (new VisitResource($visit))->response()->setStatusCode(201);
    }

    public function show(Visit $visit): VisitResource
    {
        return new VisitResource($visit);
    }

    public function update(UpdateVisitRequest $request, Visit $visit, VisitService $service): VisitResource
    {
        $data = $request->validated();

        // Gerbang pulang tidak boleh dilewati lewat edit bebas: bed harus
        // dibebaskan dan rekam pulang dibuat oleh VisitService::discharge().
        abort_if(
            array_key_exists('discharged_at', $data) || ($data['status'] ?? null) === 'discharged',
            422,
            'Pulangkan kunjungan melalui POST /visits/{visit}/discharge.',
        );

        // Gerbang mutasi bed/ward tidak boleh dilewati lewat edit bebas:
        // okupansi, riwayat, dan validasi bed hanya berlaku di
        // VisitService::transfer() (#11).
        abort_if(
            array_key_exists('ward_id', $data) || array_key_exists('bed_id', $data),
            422,
            'Pindahkan bed/ward kunjungan melalui POST /visits/{visit}/transfer.',
        );

        // Status lain (mis. batal) juga punya gerbang sendiri (bed dibebaskan,
        // tagihan terkunci diperiksa) — tidak boleh diubah lewat edit bebas.
        abort_if(
            array_key_exists('status', $data),
            422,
            'Ubah status kunjungan melalui gerbang khusus (mis. DELETE /visits/{visit} untuk batal, POST /visits/{visit}/discharge untuk pulang).',
        );

        $visit = $service->updateDetails($visit, $data);

        return new VisitResource($visit);
    }

    public function destroy(Visit $visit, VisitService $service): JsonResponse
    {
        // Bukan hard delete: lewat VisitService::cancel() agar bed dibebaskan
        // dan gerbang tagihan terkunci tetap berlaku (konsisten dgn store/transfer/discharge).
        $service->cancel($visit);

        return response()->json(null, 204);
    }

    /** Gerbang mutasi antar bed (#11). */
    public function transfer(Request $request, Visit $visit, VisitService $service): JsonResponse
    {
        $data = $request->validate([
            'target_bed_id' => ['required', 'integer', 'exists:beds,id'],
            'notes' => ['nullable', 'string'],
        ]);

        $transfer = $service->transfer($visit, (int) $data['target_bed_id'], $request->user(), $data['notes'] ?? null);

        return response()->json([
            'data' => [
                'id' => $transfer->id,
                'visit_id' => $transfer->visit_id,
                'ward_from_id' => $transfer->ward_from_id,
                'bed_from_id' => $transfer->bed_from_id,
                'ward_to_id' => $transfer->ward_to_id,
                'bed_to_id' => $transfer->bed_to_id,
                'transferred_at' => $transfer->transferred_at?->toIso8601String(),
            ],
        ], 201);
    }

    /** Gerbang pulang (#11): bebaskan bed + rekam discharge + akomodasi. */
    public function discharge(Request $request, Visit $visit, VisitService $service): VisitResource
    {
        $data = $request->validate([
            'final_outcome' => ['required', 'string', 'max:255'],
            // Skema patient_discharge_records.discharge_method NOT NULL.
            'discharge_method' => ['required', 'string', 'max:255'],
            'follow_up_notes' => ['nullable', 'string'],
        ]);

        $visit = $service->discharge(
            $visit,
            $data['final_outcome'],
            $request->user(),
            $data['discharge_method'] ?? null,
            $data['follow_up_notes'] ?? null,
        );

        return new VisitResource($visit);
    }
}
