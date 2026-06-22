<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordObstetrics\Http\Controllers\ObstetricsController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('obstetrics-records', ObstetricsController::class)->only(['index', 'show'])->parameters(['obstetrics-records' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('obstetrics-records', ObstetricsController::class)->only(['store', 'update', 'destroy'])->parameters(['obstetrics-records' => 'record']);
    });
});
