<?php

namespace Modules\KemkesReport\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\KemkesReport\Services\KemkesReportService;

/**
 * Endpoint RL SIRS lokal (JSON siap kirim) — integrasi web-service SIRS Online
 * Kemkes adalah cakupan ekor panjang, bukan bagian modul ini.
 */
class KemkesReportController extends Controller
{
    public function __construct(private readonly KemkesReportService $reports)
    {
    }

    public function bedOccupancy(Request $request): JsonResponse
    {
        $request->validate(['date' => ['nullable', 'date']]);

        return response()->json(['data' => $this->reports->bedOccupancy($request->input('date'))]);
    }

    public function inpatientIndicators(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'from' => ['required', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        return response()->json([
            'data' => $this->reports->inpatientIndicators($validated['from'], $validated['to'] ?? null),
        ]);
    }

    public function inpatientVisitsByClass(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'from' => ['required', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        return response()->json([
            'data' => $this->reports->inpatientVisitsByClass($validated['from'], $validated['to'] ?? null),
        ]);
    }
}
