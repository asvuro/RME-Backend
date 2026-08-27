<?php

use Illuminate\Support\Facades\Route;
use Modules\PendaftaranPatientGuardian\Http\Controllers\PatientGuardianController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('patient-guardians', PatientGuardianController::class)->only(['index', 'show']);

    Route::apiResource('patient-guardians', PatientGuardianController::class)->only(['store', 'update', 'destroy']);
});
