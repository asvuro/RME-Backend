<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordFingerExamination\Http\Controllers\FingerExaminationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('finger-examinations', FingerExaminationController::class)->only(['index', 'show'])->parameters(['finger-examinations' => 'record']);

    Route::apiResource('finger-examinations', FingerExaminationController::class)->only(['store', 'update', 'destroy'])->parameters(['finger-examinations' => 'record']);
});
