<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralQuarter\Http\Controllers\QuarterController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('quarters', QuarterController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('quarters', QuarterController::class)->only(['store', 'update', 'destroy']);
    });
});
