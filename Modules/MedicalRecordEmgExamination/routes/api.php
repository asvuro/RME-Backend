<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordEmgExamination\Http\Controllers\EmgExaminationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('emg-examinations', EmgExaminationController::class)->only(['index', 'show'])->parameters(['emg-examinations' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('emg-examinations', EmgExaminationController::class)->only(['store', 'update', 'destroy'])->parameters(['emg-examinations' => 'record']);
    });
});
