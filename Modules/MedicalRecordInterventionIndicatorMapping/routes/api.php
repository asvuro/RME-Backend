<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordInterventionIndicatorMapping\Http\Controllers\InterventionIndicatorMappingController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('intervention-indicator-mappings', InterventionIndicatorMappingController::class)->only(['index', 'show'])->parameters(['intervention-indicator-mappings' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('intervention-indicator-mappings', InterventionIndicatorMappingController::class)->only(['store', 'update', 'destroy'])->parameters(['intervention-indicator-mappings' => 'record']);
    });
});
