<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordMchatAssessmentExamination\Http\Controllers\MchatAssessmentExaminationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('mchat-assessment-examinations', MchatAssessmentExaminationController::class)->only(['index', 'show'])->parameters(['mchat-assessment-examinations' => 'record']);

    Route::apiResource('mchat-assessment-examinations', MchatAssessmentExaminationController::class)->only(['store', 'update', 'destroy'])->parameters(['mchat-assessment-examinations' => 'record']);
});
