<?php

use Illuminate\Support\Facades\Route;
use Modules\LayananMedicalProcedure\Http\Controllers\MedicalProcedureController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('medical-procedures', MedicalProcedureController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('medical-procedures', MedicalProcedureController::class)->only(['store', 'update']);
    });
});
