<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordEegExamination\Http\Controllers\EegExaminationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('eeg-examinations', EegExaminationController::class)->only(['index', 'show'])->parameters(['eeg-examinations' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('eeg-examinations', EegExaminationController::class)->only(['store', 'update', 'destroy'])->parameters(['eeg-examinations' => 'record']);
    });
});
