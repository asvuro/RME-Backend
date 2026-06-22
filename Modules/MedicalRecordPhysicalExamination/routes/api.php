<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordPhysicalExamination\Http\Controllers\PhysicalExaminationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('physical-examinations', PhysicalExaminationController::class)->only(['index', 'show'])->parameters(['physical-examinations' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('physical-examinations', PhysicalExaminationController::class)->only(['store', 'update', 'destroy'])->parameters(['physical-examinations' => 'record']);
    });
});
