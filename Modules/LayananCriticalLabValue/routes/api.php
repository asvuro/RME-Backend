<?php

use Illuminate\Support\Facades\Route;
use Modules\LayananCriticalLabValue\Http\Controllers\CriticalLabValueController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('critical-lab-values', CriticalLabValueController::class)->only(['index', 'show'])->parameters(['critical-lab-values' => 'critical_value']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('critical-lab-values', CriticalLabValueController::class)->only(['store', 'update'])->parameters(['critical-lab-values' => 'critical_value']);
    });
});
