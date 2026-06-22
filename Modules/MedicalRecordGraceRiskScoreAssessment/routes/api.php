<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordGraceRiskScoreAssessment\Http\Controllers\GraceRiskScoreAssessmentController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('grace-risk-score-assessments', GraceRiskScoreAssessmentController::class)->only(['index', 'show'])->parameters(['grace-risk-score-assessments' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('grace-risk-score-assessments', GraceRiskScoreAssessmentController::class)->only(['store', 'update', 'destroy'])->parameters(['grace-risk-score-assessments' => 'record']);
    });
});
