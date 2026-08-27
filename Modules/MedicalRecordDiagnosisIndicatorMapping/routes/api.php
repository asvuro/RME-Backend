<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordDiagnosisIndicatorMapping\Http\Controllers\DiagnosisIndicatorMappingController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('diagnosis-indicator-mappings', DiagnosisIndicatorMappingController::class)->only(['index', 'show'])->parameters(['diagnosis-indicator-mappings' => 'record']);

    Route::apiResource('diagnosis-indicator-mappings', DiagnosisIndicatorMappingController::class)->only(['store', 'update', 'destroy'])->parameters(['diagnosis-indicator-mappings' => 'record']);
});
