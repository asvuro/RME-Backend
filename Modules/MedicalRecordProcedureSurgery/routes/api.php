<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordProcedureSurgery\Http\Controllers\ProcedureSurgeryController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('procedure-surgeries', ProcedureSurgeryController::class)->only(['index', 'show'])->parameters(['procedure-surgeries' => 'record']);

    Route::apiResource('procedure-surgeries', ProcedureSurgeryController::class)->only(['store', 'update', 'destroy'])->parameters(['procedure-surgeries' => 'record']);
});
