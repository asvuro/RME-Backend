<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordFunctionalStatusAssessment\Http\Controllers\FunctionalStatusAssessmentController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('functional-status-assessments', FunctionalStatusAssessmentController::class)->only(['index', 'show'])->parameters(['functional-status-assessments' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('functional-status-assessments', FunctionalStatusAssessmentController::class)->only(['store'])->parameters(['functional-status-assessments' => 'record']);
    });
});
