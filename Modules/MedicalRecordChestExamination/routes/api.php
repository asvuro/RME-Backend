<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordChestExamination\Http\Controllers\ChestExaminationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('chest-examinations', ChestExaminationController::class)->only(['index', 'show'])->parameters(['chest-examinations' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('chest-examinations', ChestExaminationController::class)->only(['store', 'update', 'destroy'])->parameters(['chest-examinations' => 'record']);
    });
});
