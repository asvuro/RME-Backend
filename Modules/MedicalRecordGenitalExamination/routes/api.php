<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordGenitalExamination\Http\Controllers\GenitalExaminationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('genital-examinations', GenitalExaminationController::class)->only(['index', 'show'])->parameters(['genital-examinations' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('genital-examinations', GenitalExaminationController::class)->only(['store', 'update', 'destroy'])->parameters(['genital-examinations' => 'record']);
    });
});
