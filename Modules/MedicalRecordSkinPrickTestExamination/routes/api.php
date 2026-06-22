<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordSkinPrickTestExamination\Http\Controllers\SkinPrickTestExaminationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('skin-prick-tests', SkinPrickTestExaminationController::class)->only(['index', 'show'])->parameters(['skin-prick-tests' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('skin-prick-tests', SkinPrickTestExaminationController::class)->only(['store', 'update', 'destroy'])->parameters(['skin-prick-tests' => 'record']);
    });
});
