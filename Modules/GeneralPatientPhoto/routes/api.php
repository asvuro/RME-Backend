<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralPatientPhoto\Http\Controllers\GeneralPatientPhotoController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('patient-photos', GeneralPatientPhotoController::class)->only(['index', 'show'])->parameters(['patient-photos' => 'patientPhoto']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('patient-photos', GeneralPatientPhotoController::class)->only(['store', 'update', 'destroy'])->parameters(['patient-photos' => 'patientPhoto']);
    });
});
