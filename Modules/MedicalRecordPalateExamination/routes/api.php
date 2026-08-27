<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordPalateExamination\Http\Controllers\PalateExaminationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('palate-examinations', PalateExaminationController::class)->only(['index', 'show'])->parameters(['palate-examinations' => 'record']);

    Route::apiResource('palate-examinations', PalateExaminationController::class)->only(['store', 'update', 'destroy'])->parameters(['palate-examinations' => 'record']);
});
