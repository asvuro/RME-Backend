<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordObstetricHistory\Http\Controllers\ObstetricHistoryController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('obstetric-histories', ObstetricHistoryController::class)->only(['index', 'show'])->parameters(['obstetric-histories' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('obstetric-histories', ObstetricHistoryController::class)->only(['store'])->parameters(['obstetric-histories' => 'record']);
    });
});
