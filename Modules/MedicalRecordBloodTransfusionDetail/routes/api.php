<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordBloodTransfusionDetail\Http\Controllers\BloodTransfusionDetailController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('blood-transfusion-details', BloodTransfusionDetailController::class)->only(['index', 'show'])->parameters([
        'blood-transfusion-details' => 'detail',
    ]);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('blood-transfusion-details', BloodTransfusionDetailController::class)->only(['store', 'update', 'destroy'])->parameters([
        'blood-transfusion-details' => 'detail',
    ]);
    });
});
