<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralPatientStatus\Http\Controllers\PatientStatusController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('patient-statuses', PatientStatusController::class)->only(['index', 'show']);

    Route::apiResource('patient-statuses', PatientStatusController::class)->only(['store', 'update', 'destroy']);
});
