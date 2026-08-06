<?php

namespace Modules\PendaftaranSelfCheckin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\PendaftaranSelfCheckin\Http\Requests\StoreSelfCheckinQueueRequest;
use Modules\PendaftaranSelfCheckin\Http\Resources\SelfCheckinQueueResource;
use Modules\PendaftaranSelfCheckin\Models\SelfCheckinQueue;
use Modules\PendaftaranSelfCheckin\Services\SelfCheckinService;

class SelfCheckinQueueController extends Controller
{
    public function __construct(protected SelfCheckinService $service) {}

    /**
     * Daftar antrian untuk layar monitor loket/kiosk. Filter ward_id + date;
     * date default HARI INI supaya layar anjungan tidak perlu kirim tanggal.
     */
    public function index(Request $request)
    {
        $query = SelfCheckinQueue::query();

        if ($request->filled('ward_id')) {
            $query->where('ward_id', $request->integer('ward_id'));
        }

        $date = $request->filled('date') ? $request->input('date') : now()->toDateString();
        $query->whereDate('queue_date', $date);

        return SelfCheckinQueueResource::collection(
            $query->orderBy('queue_number')->paginate($request->integer('per_page', 15)),
        );
    }

    /**
     * Check-in pasien dari kiosk. Controller tidak pernah create model
     * langsung: penomoran harian dan gerbang anti-antrian-ganda ada di
     * SelfCheckinService (lihat docblock service).
     */
    public function store(StoreSelfCheckinQueueRequest $request): JsonResponse
    {
        $queue = $this->service->checkIn($request->validated());

        return (new SelfCheckinQueueResource($queue))->response()->setStatusCode(201);
    }

    /**
     * Petugas loket memanggil nomor. Transisi waiting -> called hanya lewat
     * service; double-call ditolak 422 di sana.
     */
    public function call(Request $request, SelfCheckinQueue $queue): SelfCheckinQueueResource
    {
        return new SelfCheckinQueueResource($this->service->call($queue, $request->user()));
    }

    /**
     * Petugas loket menyelesaikan pasien yang sudah dipanggil.
     * Complete tanpa call ditolak 422 oleh gerbang urutan di service.
     */
    public function complete(Request $request, SelfCheckinQueue $queue): SelfCheckinQueueResource
    {
        return new SelfCheckinQueueResource($this->service->complete($queue, $request->user()));
    }
}
