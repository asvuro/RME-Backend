<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordBreastExamination\Http\Controllers\BreastExaminationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('breast-examinations', BreastExaminationController::class)->only(['index', 'show'])->parameters(['breast-examinations' => 'record']);

    Route::apiResource('breast-examinations', BreastExaminationController::class)->only(['store', 'update', 'destroy'])->parameters(['breast-examinations' => 'record']);
});
