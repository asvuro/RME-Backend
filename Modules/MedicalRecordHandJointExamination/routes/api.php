<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordHandJointExamination\Http\Controllers\HandJointExaminationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('hand-joint-examinations', HandJointExaminationController::class)->only(['index', 'show'])->parameters(['hand-joint-examinations' => 'record']);

    Route::apiResource('hand-joint-examinations', HandJointExaminationController::class)->only(['store', 'update', 'destroy'])->parameters(['hand-joint-examinations' => 'record']);
});
