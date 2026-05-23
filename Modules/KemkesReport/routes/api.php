<?php

use Illuminate\Support\Facades\Route;
use Modules\KemkesReport\Http\Controllers\KemkesReportController;

// Laporan operasional untuk seluruh staf terautentikasi — bukan role:admin
// (jejak audit #12 saja yang dikunci admin).
Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::get('kemkes-reports/bed-occupancy', [KemkesReportController::class, 'bedOccupancy']);
    Route::get('kemkes-reports/inpatient-indicators', [KemkesReportController::class, 'inpatientIndicators']);
    Route::get('kemkes-reports/inpatient-visits-by-class', [KemkesReportController::class, 'inpatientVisitsByClass']);
});
