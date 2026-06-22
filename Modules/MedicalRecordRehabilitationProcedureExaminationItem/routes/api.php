<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordRehabilitationProcedureExaminationItem\Http\Controllers\RehabilitationProcedureExaminationItemController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('rehab-procedure-examination-items', RehabilitationProcedureExaminationItemController::class)->only(['index', 'show'])->parameters(['rehab-procedure-examination-items' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('rehab-procedure-examination-items', RehabilitationProcedureExaminationItemController::class)->only(['store', 'update', 'destroy'])->parameters(['rehab-procedure-examination-items' => 'record']);
    });
});
