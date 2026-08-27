<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordBackExamination\Http\Controllers\BackExaminationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('back-examinations', BackExaminationController::class)->only(['index', 'show'])->parameters(['back-examinations' => 'record']);

    Route::apiResource('back-examinations', BackExaminationController::class)->only(['store', 'update', 'destroy'])->parameters(['back-examinations' => 'record']);
});
