<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralWardVisitType\Http\Controllers\WardVisitTypeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('ward-visit-types', WardVisitTypeController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('ward-visit-types', WardVisitTypeController::class)->only(['store', 'update', 'destroy']);
    });
});
