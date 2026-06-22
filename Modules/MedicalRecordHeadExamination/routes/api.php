<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordHeadExamination\Http\Controllers\HeadExaminationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('head-examinations', HeadExaminationController::class)->only(['index', 'show'])->parameters(['head-examinations' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('head-examinations', HeadExaminationController::class)->only(['store', 'update', 'destroy'])->parameters(['head-examinations' => 'record']);
    });
});
