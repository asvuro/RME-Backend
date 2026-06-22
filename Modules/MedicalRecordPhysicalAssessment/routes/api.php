<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordPhysicalAssessment\Http\Controllers\PhysicalAssessmentController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('physical-assessments', PhysicalAssessmentController::class)->only(['index', 'show'])->parameters(['physical-assessments' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('physical-assessments', PhysicalAssessmentController::class)->only(['store', 'update', 'destroy'])->parameters(['physical-assessments' => 'record']);
    });
});
