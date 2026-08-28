<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralPatientFamily\Http\Controllers\PatientFamilyController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('patientfamilies', PatientFamilyController::class)->names('generalpatientfamily.patientfamilies')->only(['index', 'show']);

    Route::apiResource('patientfamilies', PatientFamilyController::class)->names('generalpatientfamily.patientfamilies')->only(['store', 'update', 'destroy']);
});
