<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralLabServiceParameter\Http\Controllers\LabServiceParameterController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('lab-service-parameters', LabServiceParameterController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('lab-service-parameters', LabServiceParameterController::class)->only(['store', 'update', 'destroy']);
    });
});
