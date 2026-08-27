<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordDiagnosis\Http\Controllers\DiagnosisController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('diagnoses', DiagnosisController::class)->only(['index', 'show']);

    Route::apiResource('diagnoses', DiagnosisController::class)->only(['store', 'destroy']);
});
