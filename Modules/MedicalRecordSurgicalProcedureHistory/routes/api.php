<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordSurgicalProcedureHistory\Http\Controllers\SurgicalProcedureHistoryController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('surgical-procedure-histories', SurgicalProcedureHistoryController::class)->only(['index', 'show'])->parameters(['surgical-procedure-histories' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('surgical-procedure-histories', SurgicalProcedureHistoryController::class)->only(['store'])->parameters(['surgical-procedure-histories' => 'record']);
    });
});
