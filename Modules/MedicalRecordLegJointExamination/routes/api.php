<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordLegJointExamination\Http\Controllers\LegJointExaminationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('leg-joint-examinations', LegJointExaminationController::class)->only(['index', 'show'])->parameters(['leg-joint-examinations' => 'record']);

    Route::apiResource('leg-joint-examinations', LegJointExaminationController::class)->only(['store', 'update', 'destroy'])->parameters(['leg-joint-examinations' => 'record']);
});
