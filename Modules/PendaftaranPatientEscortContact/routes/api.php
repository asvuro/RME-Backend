<?php

use Illuminate\Support\Facades\Route;
use Modules\PendaftaranPatientEscortContact\Http\Controllers\PatientEscortContactController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('patient-escort-contacts', PatientEscortContactController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('patient-escort-contacts', PatientEscortContactController::class)->only(['store', 'update', 'destroy']);
    });
});
