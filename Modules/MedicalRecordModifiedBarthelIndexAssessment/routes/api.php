<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordModifiedBarthelIndexAssessment\Http\Controllers\ModifiedBarthelIndexAssessmentController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('modified-barthel-index-assessments', ModifiedBarthelIndexAssessmentController::class)->only(['index', 'show'])->parameters(['modified-barthel-index-assessments' => 'record']);

    Route::apiResource('modified-barthel-index-assessments', ModifiedBarthelIndexAssessmentController::class)->only(['store', 'update', 'destroy'])->parameters(['modified-barthel-index-assessments' => 'record']);
});
