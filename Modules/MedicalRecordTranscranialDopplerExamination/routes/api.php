<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordTranscranialDopplerExamination\Http\Controllers\TranscranialDopplerExaminationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('tcd-examinations', TranscranialDopplerExaminationController::class)->only(['index', 'show'])->parameters(['tcd-examinations' => 'record']);

    Route::apiResource('tcd-examinations', TranscranialDopplerExaminationController::class)->only(['store', 'update', 'destroy'])->parameters(['tcd-examinations' => 'record']);
});
