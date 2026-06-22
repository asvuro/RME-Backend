<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordPharynxExamination\Http\Controllers\PharynxExaminationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('pharynx-examinations', PharynxExaminationController::class)->only(['index', 'show'])->parameters(['pharynx-examinations' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('pharynx-examinations', PharynxExaminationController::class)->only(['store', 'update', 'destroy'])->parameters(['pharynx-examinations' => 'record']);
    });
});
