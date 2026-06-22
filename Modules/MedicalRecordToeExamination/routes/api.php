<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordToeExamination\Http\Controllers\ToeExaminationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('toe-examinations', ToeExaminationController::class)->only(['index', 'show'])->parameters(['toe-examinations' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('toe-examinations', ToeExaminationController::class)->only(['store', 'update', 'destroy'])->parameters(['toe-examinations' => 'record']);
    });
});
