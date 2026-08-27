<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordDifferentialDiagnosis\Http\Controllers\DifferentialDiagnosisController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('differential-diagnoses', DifferentialDiagnosisController::class)->only(['index', 'show'])->parameters(['differential-diagnoses' => 'record']);

    Route::apiResource('differential-diagnoses', DifferentialDiagnosisController::class)->only(['store', 'update', 'destroy'])->parameters(['differential-diagnoses' => 'record']);
});
