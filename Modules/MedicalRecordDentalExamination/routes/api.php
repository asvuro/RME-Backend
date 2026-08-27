<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordDentalExamination\Http\Controllers\DentalExaminationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('dental-examinations', DentalExaminationController::class)->only(['index', 'show'])->parameters(['dental-examinations' => 'record']);

    Route::apiResource('dental-examinations', DentalExaminationController::class)->only(['store', 'update', 'destroy'])->parameters(['dental-examinations' => 'record']);
});
