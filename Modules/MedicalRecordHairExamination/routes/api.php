<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordHairExamination\Http\Controllers\HairExaminationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('hair-examinations', HairExaminationController::class)->only(['index', 'show'])->parameters(['hair-examinations' => 'record']);

    Route::apiResource('hair-examinations', HairExaminationController::class)->only(['store', 'update', 'destroy'])->parameters(['hair-examinations' => 'record']);
});
