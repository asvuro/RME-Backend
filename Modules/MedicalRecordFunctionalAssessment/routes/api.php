<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordFunctionalAssessment\Http\Controllers\FunctionalAssessmentController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('functional-assessments', FunctionalAssessmentController::class)->only(['index', 'show'])->parameters(['functional-assessments' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('functional-assessments', FunctionalAssessmentController::class)->only(['store', 'update', 'destroy'])->parameters(['functional-assessments' => 'record']);
    });
});
