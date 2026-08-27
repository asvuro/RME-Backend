<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordEarExamination\Http\Controllers\EarExaminationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('ear-examinations', EarExaminationController::class)->only(['index', 'show'])->parameters(['ear-examinations' => 'record']);

    Route::apiResource('ear-examinations', EarExaminationController::class)->only(['store', 'update', 'destroy'])->parameters(['ear-examinations' => 'record']);
});
