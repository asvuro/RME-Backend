<?php

namespace Modules\AuditInfectionSurveillance\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\AuditInfectionSurveillance\Http\Requests\StoreInfectionCaseRequest;
use Modules\AuditInfectionSurveillance\Http\Requests\UpdateInfectionCaseRequest;
use Modules\AuditInfectionSurveillance\Models\InfectionCase;
use Modules\AuditInfectionSurveillance\Services\SurveillanceService;

class InfectionCaseController extends Controller
{
    public function __construct(protected SurveillanceService $surveillanceService) {}

    public function index(Request $request)
    {
        $query = InfectionCase::query()->with(['visit', 'relatedDeviceDay']);

        if ($request->filled('visit_id')) {
            $query->where('visit_id', $request->integer('visit_id'));
        }

        if ($request->filled('infection_type')) {
            $query->where('infection_type', $request->input('infection_type'));
        }

        return $query->latest('diagnosed_at')->paginate($request->integer('per_page', 15));
    }

    public function store(StoreInfectionCaseRequest $request): JsonResponse
    {
        // Gerbang konsistensi rujukan (kunjungan sama + jenis alat cocok) ada
        // di SurveillanceService::createInfectionCase().
        $case = $this->surveillanceService->createInfectionCase($request->validated());

        return response()->json($case, 201);
    }

    public function show(InfectionCase $infectionCase): InfectionCase
    {
        return $infectionCase->load(['visit', 'relatedDeviceDay']);
    }

    public function update(UpdateInfectionCaseRequest $request, InfectionCase $infectionCase): InfectionCase
    {
        return $this->surveillanceService->updateInfectionCase($infectionCase, $request->validated());
    }

    public function destroy(InfectionCase $infectionCase): JsonResponse
    {
        $this->surveillanceService->deleteInfectionCase($infectionCase);

        return response()->json(null, 204);
    }
}
