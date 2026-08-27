<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordPressureUlcerRiskAssessment\Http\Controllers\PressureUlcerRiskAssessmentController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('pressure-ulcer-risk-assessments', PressureUlcerRiskAssessmentController::class)->only(['index', 'show'])->parameters(['pressure-ulcer-risk-assessments' => 'record']);

    Route::apiResource('pressure-ulcer-risk-assessments', PressureUlcerRiskAssessmentController::class)->only(['store', 'update', 'destroy'])->parameters(['pressure-ulcer-risk-assessments' => 'record']);
});
