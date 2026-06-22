<?php

use Illuminate\Support\Facades\Route;
use Modules\PendaftaranPatientGuardianContact\Http\Controllers\PatientGuardianContactController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('patient-guardian-contacts', PatientGuardianContactController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('patient-guardian-contacts', PatientGuardianContactController::class)->only(['store', 'update', 'destroy']);
    });
});
