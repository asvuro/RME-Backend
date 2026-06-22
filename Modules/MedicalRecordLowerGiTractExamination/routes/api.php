<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordLowerGiTractExamination\Http\Controllers\LowerGiTractExaminationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('lower-gi-examinations', LowerGiTractExaminationController::class)->only(['index', 'show'])->parameters(['lower-gi-examinations' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('lower-gi-examinations', LowerGiTractExaminationController::class)->only(['store', 'update', 'destroy'])->parameters(['lower-gi-examinations' => 'record']);
    });
});
