<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordPreAnesthesiaSedationAssessment\Http\Controllers\PreAnesthesiaSedationAssessmentController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('pre-anesthesia-sedation-assessments', PreAnesthesiaSedationAssessmentController::class)->only(['index', 'show'])->parameters(['pre-anesthesia-sedation-assessments' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('pre-anesthesia-sedation-assessments', PreAnesthesiaSedationAssessmentController::class)->only(['store'])->parameters(['pre-anesthesia-sedation-assessments' => 'record']);
    });
});
