<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralPatientType\Http\Controllers\PatientTypeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('patient-types', PatientTypeController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('patient-types', PatientTypeController::class)->only(['store', 'update', 'destroy']);
    });
});
