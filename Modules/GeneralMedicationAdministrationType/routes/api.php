<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralMedicationAdministrationType\Http\Controllers\MedicationAdministrationTypeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('medication-administration-types', MedicationAdministrationTypeController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('medication-administration-types', MedicationAdministrationTypeController::class)->only(['store', 'update', 'destroy']);
    });
});
