<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordNoseExamination\Http\Controllers\NoseExaminationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('nose-examinations', NoseExaminationController::class)->only(['index', 'show'])->parameters(['nose-examinations' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('nose-examinations', NoseExaminationController::class)->only(['store', 'update', 'destroy'])->parameters(['nose-examinations' => 'record']);
    });
});
