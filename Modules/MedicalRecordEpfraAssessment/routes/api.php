<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordEpfraAssessment\Http\Controllers\EpfraAssessmentController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('epfra-assessments', EpfraAssessmentController::class)->only(['index', 'show'])->parameters(['epfra-assessments' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('epfra-assessments', EpfraAssessmentController::class)->only(['store', 'update', 'destroy'])->parameters(['epfra-assessments' => 'record']);
    });
});
