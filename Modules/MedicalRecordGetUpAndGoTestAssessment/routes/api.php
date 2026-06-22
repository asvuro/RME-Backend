<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordGetUpAndGoTestAssessment\Http\Controllers\GetUpAndGoTestAssessmentController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('get-up-and-go-assessments', GetUpAndGoTestAssessmentController::class)->only(['index', 'show'])->parameters(['get-up-and-go-assessments' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('get-up-and-go-assessments', GetUpAndGoTestAssessmentController::class)->only(['store', 'update', 'destroy'])->parameters(['get-up-and-go-assessments' => 'record']);
    });
});
