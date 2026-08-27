<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordRehabilitationProcedureExamination\Http\Controllers\RehabilitationProcedureExaminationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('rehab-procedure-examinations', RehabilitationProcedureExaminationController::class)->only(['index', 'show'])->parameters(['rehab-procedure-examinations' => 'record']);

    Route::apiResource('rehab-procedure-examinations', RehabilitationProcedureExaminationController::class)->only(['store', 'update', 'destroy'])->parameters(['rehab-procedure-examinations' => 'record']);
});
