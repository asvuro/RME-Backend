<?php

use Illuminate\Support\Facades\Route;
use Modules\LayananRadiologyViewerLog\Http\Controllers\RadiologyViewerLogController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('radiology-viewer-logs', RadiologyViewerLogController::class)->only(['index', 'show'])->parameters(['radiology-viewer-logs' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('radiology-viewer-logs', RadiologyViewerLogController::class)->only(['store', 'destroy'])->parameters(['radiology-viewer-logs' => 'record']);
    });
});
