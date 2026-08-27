<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordFingernailExamination\Http\Controllers\FingernailExaminationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('fingernail-examinations', FingernailExaminationController::class)->only(['index', 'show'])->parameters(['fingernail-examinations' => 'record']);

    Route::apiResource('fingernail-examinations', FingernailExaminationController::class)->only(['store', 'update', 'destroy'])->parameters(['fingernail-examinations' => 'record']);
});
