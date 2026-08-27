<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordForearmExamination\Http\Controllers\ForearmExaminationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('forearm-examinations', ForearmExaminationController::class)->only(['index', 'show'])->parameters(['forearm-examinations' => 'record']);

    Route::apiResource('forearm-examinations', ForearmExaminationController::class)->only(['store', 'update', 'destroy'])->parameters(['forearm-examinations' => 'record']);
});
