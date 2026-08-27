<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordToenailExamination\Http\Controllers\ToenailExaminationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('toenail-examinations', ToenailExaminationController::class)->only(['index', 'show'])->parameters(['toenail-examinations' => 'record']);

    Route::apiResource('toenail-examinations', ToenailExaminationController::class)->only(['store', 'update', 'destroy'])->parameters(['toenail-examinations' => 'record']);
});
