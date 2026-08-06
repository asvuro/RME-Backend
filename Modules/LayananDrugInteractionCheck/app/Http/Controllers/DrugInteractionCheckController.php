<?php

namespace Modules\LayananDrugInteractionCheck\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\LayananDrugInteractionCheck\Services\DrugInteractionCheckService;
use Modules\LayananPrescription\Models\Prescription;

/**
 * Endpoint advisory tunggal: GET prescriptions/{prescription}/interaction-check.
 * Sengaja tidak punya aksi tulis apa pun - modul ini tidak boleh menyentuh
 * state resep/dispense (lihat DispenseService yang alurnya tidak diubah).
 */
class DrugInteractionCheckController extends Controller
{
    public function __construct(protected DrugInteractionCheckService $service)
    {
    }

    public function __invoke(Request $request, Prescription $prescription): JsonResponse
    {
        return response()->json([
            'data' => $this->service->checkPrescription($prescription->getKey()),
        ]);
    }
}
