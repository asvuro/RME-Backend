<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralPatientAccessLock\Http\Controllers\PatientAccessLockController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('patient-access-locks', PatientAccessLockController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('patient-access-locks', PatientAccessLockController::class)->only(['store', 'update', 'destroy']);
    });
});
