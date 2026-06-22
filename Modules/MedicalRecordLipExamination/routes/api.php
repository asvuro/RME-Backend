<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordLipExamination\Http\Controllers\LipExaminationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('lip-examinations', LipExaminationController::class)->only(['index', 'show'])->parameters(['lip-examinations' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('lip-examinations', LipExaminationController::class)->only(['store', 'update', 'destroy'])->parameters(['lip-examinations' => 'record']);
    });
});
