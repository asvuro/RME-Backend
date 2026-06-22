<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordIntradialyticHdMonitoring\Http\Controllers\IntradialyticHdMonitoringController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('intradialytic-hd-monitorings', IntradialyticHdMonitoringController::class)->only(['index', 'show'])->parameters(['intradialytic-hd-monitorings' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('intradialytic-hd-monitorings', IntradialyticHdMonitoringController::class)->only(['store', 'update', 'destroy'])->parameters(['intradialytic-hd-monitorings' => 'record']);
    });
});
