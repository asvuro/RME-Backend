<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordEkgExamination\Http\Controllers\EkgExaminationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('ekg-examinations', EkgExaminationController::class)->only(['index', 'show'])->parameters(['ekg-examinations' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('ekg-examinations', EkgExaminationController::class)->only(['store', 'update', 'destroy'])->parameters(['ekg-examinations' => 'record']);
    });
});
