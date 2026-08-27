<?php

use Illuminate\Support\Facades\Route;
use Modules\AuditQualityIndicator\Http\Controllers\QualityIndicatorController;
use Modules\AuditQualityIndicator\Http\Controllers\QualityIndicatorRecordController;

/*
 * Nama param route dipatok eksplisit di bawah 32 karakter (jebakan param
 * rute Symfony): quality_indicator (17) dan quality_indicator_record (24).
 */
Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    // Baca-saja: tren + master + catatan capaian.
    Route::get('quality-indicators/{quality_indicator}/trend', [QualityIndicatorController::class, 'trend'])
        ->name('quality-indicators.trend');

    Route::apiResource('quality-indicators', QualityIndicatorController::class)
        ->only(['index', 'show'])
        ->parameters(['quality-indicators' => 'quality_indicator']);

    Route::apiResource('quality-indicator-records', QualityIndicatorRecordController::class)
        ->only(['index', 'show'])
        ->parameters(['quality-indicator-records' => 'quality_indicator_record']);

    Route::apiResource('quality-indicators', QualityIndicatorController::class)
        ->only(['store', 'update', 'destroy'])
        ->parameters(['quality-indicators' => 'quality_indicator']);

    Route::apiResource('quality-indicator-records', QualityIndicatorRecordController::class)
        ->only(['store', 'update', 'destroy'])
        ->parameters(['quality-indicator-records' => 'quality_indicator_record']);
});
