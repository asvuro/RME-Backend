<?php

use Illuminate\Support\Facades\Route;
use Modules\LayananRadiologyResult\Http\Controllers\RadiologyResultController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('radiology-results', RadiologyResultController::class)->only(['index', 'show'])->parameters(['radiology-results' => 'rad_result']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('radiology-results', RadiologyResultController::class)->only(['store', 'update'])->parameters(['radiology-results' => 'rad_result']);
    });
});
