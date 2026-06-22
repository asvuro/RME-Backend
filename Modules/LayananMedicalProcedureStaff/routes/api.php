<?php

use Illuminate\Support\Facades\Route;
use Modules\LayananMedicalProcedureStaff\Http\Controllers\MedicalProcedureStaffController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('medical-procedure-staff', MedicalProcedureStaffController::class)->only(['index', 'show'])->parameters(['medical-procedure-staff' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('medical-procedure-staff', MedicalProcedureStaffController::class)->only(['store', 'update', 'destroy'])->parameters(['medical-procedure-staff' => 'record']);
    });
});
