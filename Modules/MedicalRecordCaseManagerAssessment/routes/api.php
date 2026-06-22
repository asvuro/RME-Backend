<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordCaseManagerAssessment\Http\Controllers\CaseManagerAssessmentController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('case-manager-assessments', CaseManagerAssessmentController::class)->only(['index', 'show'])->parameters(['case-manager-assessments' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('case-manager-assessments', CaseManagerAssessmentController::class)->only(['store', 'update', 'destroy'])->parameters(['case-manager-assessments' => 'record']);
    });
});
