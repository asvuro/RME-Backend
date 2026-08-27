<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordCoughAssessment\Http\Controllers\CoughAssessmentController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('cough-assessments', CoughAssessmentController::class)->only(['index', 'show'])->parameters(['cough-assessments' => 'record']);

    Route::apiResource('cough-assessments', CoughAssessmentController::class)->only(['store', 'update', 'destroy'])->parameters(['cough-assessments' => 'record']);
});
