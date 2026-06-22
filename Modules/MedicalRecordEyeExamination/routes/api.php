<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordEyeExamination\Http\Controllers\EyeExaminationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('eye-examinations', EyeExaminationController::class)->only(['index', 'show'])->parameters(['eye-examinations' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('eye-examinations', EyeExaminationController::class)->only(['store', 'update', 'destroy'])->parameters(['eye-examinations' => 'record']);
    });
});
