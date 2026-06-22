<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordFamilyMedicalHistory\Http\Controllers\FamilyMedicalHistoryController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('family-medical-histories', FamilyMedicalHistoryController::class)->only(['index', 'show'])->parameters(['family-medical-histories' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('family-medical-histories', FamilyMedicalHistoryController::class)->only(['store'])->parameters(['family-medical-histories' => 'record']);
    });
});
