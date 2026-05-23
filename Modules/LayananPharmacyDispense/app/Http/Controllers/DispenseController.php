<?php

namespace Modules\LayananPharmacyDispense\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Auth\Models\User;
use Modules\LayananPharmacyDispense\Http\Resources\PharmacyDispenseResource;
use Modules\LayananPharmacyDispense\Services\DispenseService;
use Modules\LayananPrescription\Models\Prescription;

/**
 * Ujung alur farmasi end-to-end: melayani resep (telaah → restriksi →
 * stok → tagihan) dalam satu gerbang di DispenseService.
 */
class DispenseController extends Controller
{
    public function __construct(protected DispenseService $service) {}

    public function store(Prescription $prescription): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();

        $dispense = $this->service->dispense($prescription, $user);

        return (new PharmacyDispenseResource($dispense))->response()->setStatusCode(201);
    }
}
