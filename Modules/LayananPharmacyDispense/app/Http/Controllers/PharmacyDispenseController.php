<?php

namespace Modules\LayananPharmacyDispense\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Auth\Models\User;
use Modules\LayananPharmacyDispense\Http\Requests\StorePharmacyDispenseRequest;
use Modules\LayananPharmacyDispense\Http\Requests\UpdatePharmacyDispenseRequest;
use Modules\LayananPharmacyDispense\Http\Resources\PharmacyDispenseResource;
use Modules\LayananPharmacyDispense\Models\PharmacyDispense;
use Modules\LayananPharmacyDispense\Services\DispenseService;
use Modules\LayananPrescription\Models\Prescription;

class PharmacyDispenseController extends Controller
{
    public function __construct(protected DispenseService $service) {}

    public function index(Request $request)
    {
        $query = PharmacyDispense::query();

        return PharmacyDispenseResource::collection($query->orderBy('id', 'desc')->paginate($request->integer('per_page', 15)));
    }

    /**
     * Membuat dispense selalu lewat DispenseService::dispense() -
     * quantity/status TIDAK diambil dari input, melainkan hasil gerbang
     * bisnis (telaah, restriksi, stok, tagihan) di service.
     */
    public function store(StorePharmacyDispenseRequest $request)
    {
        $prescription = Prescription::findOrFail($request->validated('prescription_id'));

        /** @var User $user */
        $user = auth()->user();

        $dispense = $this->service->dispense($prescription, $user);

        return (new PharmacyDispenseResource($dispense))->response()->setStatusCode(201);
    }

    public function show(PharmacyDispense $dispense): PharmacyDispenseResource
    {
        return new PharmacyDispenseResource($dispense);
    }

    /**
     * Satu-satunya transisi status yang diizinkan lewat endpoint ini adalah
     * pembatalan (status=cancelled), dieksekusi lewat DispenseService::cancel()
     * agar efek samping (kembalikan stok, sinkron status resep) tetap terjaga.
     */
    public function update(UpdatePharmacyDispenseRequest $request, PharmacyDispense $dispense): PharmacyDispenseResource
    {
        $data = $request->validated();

        abort_unless(
            ($data['status'] ?? null) === 'cancelled' && count($data) === 1,
            422,
            'Hanya pembatalan (status=cancelled) yang diizinkan lewat endpoint ini.'
        );

        /** @var User $user */
        $user = auth()->user();

        $dispense = $this->service->cancel($dispense, $user);

        return new PharmacyDispenseResource($dispense);
    }
}
