<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralLaboratoryUnit\Http\Controllers\LaboratoryUnitController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('laboratory-units', LaboratoryUnitController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('laboratory-units', LaboratoryUnitController::class)->only(['store', 'update', 'destroy']);
    });
});
