<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordHumptyDumptyFallScaleAssessment\Http\Controllers\HumptyDumptyFallScaleAssessmentController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('humpty-dumpty-fall-scale-assessments', HumptyDumptyFallScaleAssessmentController::class)->only(['index', 'show'])->parameters(['humpty-dumpty-fall-scale-assessments' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('humpty-dumpty-fall-scale-assessments', HumptyDumptyFallScaleAssessmentController::class)->only(['store'])->parameters(['humpty-dumpty-fall-scale-assessments' => 'record']);
    });
});
