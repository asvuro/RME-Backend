<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralMedicalPersonnelType\Http\Controllers\MedicalPersonnelTypeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('medical-personnel-types', MedicalPersonnelTypeController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('medical-personnel-types', MedicalPersonnelTypeController::class)->only(['store', 'update', 'destroy']);
    });
});
