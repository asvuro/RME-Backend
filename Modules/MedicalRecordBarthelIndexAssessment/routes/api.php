<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordBarthelIndexAssessment\Http\Controllers\BarthelIndexAssessmentController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('barthel-index-assessments', BarthelIndexAssessmentController::class)->only(['index', 'show'])->parameters(['barthel-index-assessments' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('barthel-index-assessments', BarthelIndexAssessmentController::class)->only(['store', 'update', 'destroy'])->parameters(['barthel-index-assessments' => 'record']);
    });
});
