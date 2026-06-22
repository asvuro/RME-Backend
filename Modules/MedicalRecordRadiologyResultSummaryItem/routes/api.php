<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordRadiologyResultSummaryItem\Http\Controllers\RadiologyResultSummaryItemController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('radiology-result-summary-items', RadiologyResultSummaryItemController::class)->only(['index', 'show'])->parameters(['radiology-result-summary-items' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('radiology-result-summary-items', RadiologyResultSummaryItemController::class)->only(['store'])->parameters(['radiology-result-summary-items' => 'record']);
    });
});
