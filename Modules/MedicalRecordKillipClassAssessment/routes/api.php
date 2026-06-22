<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordKillipClassAssessment\Http\Controllers\KillipClassAssessmentController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('killip-class-assessments', KillipClassAssessmentController::class)->only(['index', 'show'])->parameters(['killip-class-assessments' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('killip-class-assessments', KillipClassAssessmentController::class)->only(['store'])->parameters(['killip-class-assessments' => 'record']);
    });
});
