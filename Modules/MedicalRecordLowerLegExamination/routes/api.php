<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordLowerLegExamination\Http\Controllers\LowerLegExaminationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('lower-leg-examinations', LowerLegExaminationController::class)->only(['index', 'show'])->parameters(['lower-leg-examinations' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('lower-leg-examinations', LowerLegExaminationController::class)->only(['store', 'update', 'destroy'])->parameters(['lower-leg-examinations' => 'record']);
    });
});
