<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralPatientPickupStatus\Http\Controllers\PatientPickupStatusController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('patient-pickup-statuses', PatientPickupStatusController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('patient-pickup-statuses', PatientPickupStatusController::class)->only(['store', 'update', 'destroy']);
    });
});
