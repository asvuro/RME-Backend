<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordAbdomenExamination\Http\Controllers\AbdomenExaminationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('abdomen-examinations', AbdomenExaminationController::class)->only(['index', 'show'])->parameters(['abdomen-examinations' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('abdomen-examinations', AbdomenExaminationController::class)->only(['store', 'update', 'destroy'])->parameters(['abdomen-examinations' => 'record']);
    });
});
