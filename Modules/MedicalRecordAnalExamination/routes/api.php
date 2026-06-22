<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordAnalExamination\Http\Controllers\AnalExaminationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('anal-examinations', AnalExaminationController::class)->only(['index', 'show'])->parameters(['anal-examinations' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('anal-examinations', AnalExaminationController::class)->only(['store', 'update', 'destroy'])->parameters(['anal-examinations' => 'record']);
    });
});
