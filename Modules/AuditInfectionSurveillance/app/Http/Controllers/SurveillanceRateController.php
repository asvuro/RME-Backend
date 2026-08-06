<?php

namespace Modules\AuditInfectionSurveillance\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Modules\AuditInfectionSurveillance\Models\InfectionCase;
use Modules\AuditInfectionSurveillance\Services\SurveillanceService;

class SurveillanceRateController extends Controller
{
    public function __construct(protected SurveillanceService $surveillanceService) {}

    /**
     * GET /api/v1/infection-surveillance/rate?type=&start=&end=
     * Baca-saja: kalkulasi laju infeksi per 1.000 hari-alat.
     */
    public function rate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'type' => ['required', 'string', Rule::in(InfectionCase::TYPES)],
            'start' => ['required', 'date'],
            'end' => ['required', 'date', 'after_or_equal:start'],
        ]);

        // Formula dan gerbang periode ada di SurveillanceService::calculateRate().
        return response()->json($this->surveillanceService->calculateRate(
            $data['type'],
            Carbon::parse($data['start']),
            Carbon::parse($data['end']),
        ));
    }
}
