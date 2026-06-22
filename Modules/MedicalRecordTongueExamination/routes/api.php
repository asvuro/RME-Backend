<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordTongueExamination\Http\Controllers\TongueExaminationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('tongue-examinations', TongueExaminationController::class)->only(['index', 'show'])->parameters(['tongue-examinations' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('tongue-examinations', TongueExaminationController::class)->only(['store', 'update', 'destroy'])->parameters(['tongue-examinations' => 'record']);
    });
});
