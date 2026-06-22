<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralVisitType\Http\Controllers\VisitTypeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('visit-types', VisitTypeController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('visit-types', VisitTypeController::class)->only(['store', 'update', 'destroy']);
    });
});
