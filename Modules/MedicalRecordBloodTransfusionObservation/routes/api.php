<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordBloodTransfusionObservation\Http\Controllers\BloodTransfusionObservationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('blood-transfusion-observations', BloodTransfusionObservationController::class)->only(['index', 'show'])->parameters(['blood-transfusion-observations' => 'record']);

    Route::apiResource('blood-transfusion-observations', BloodTransfusionObservationController::class)->only(['store', 'update', 'destroy'])->parameters(['blood-transfusion-observations' => 'record']);
});
