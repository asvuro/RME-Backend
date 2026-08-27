<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordPharmacyDiagnosis\Http\Controllers\PharmacyDiagnosisController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('pharmacy-diagnoses', PharmacyDiagnosisController::class)->only(['index', 'show'])->parameters(['pharmacy-diagnoses' => 'record']);

    Route::apiResource('pharmacy-diagnoses', PharmacyDiagnosisController::class)->only(['store', 'update', 'destroy'])->parameters(['pharmacy-diagnoses' => 'record']);
});
