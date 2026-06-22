<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordFibroscanResult\Http\Controllers\FibroscanResultController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('fibroscan-results', FibroscanResultController::class)->only(['index', 'show'])->parameters(['fibroscan-results' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('fibroscan-results', FibroscanResultController::class)->only(['store', 'update', 'destroy'])->parameters(['fibroscan-results' => 'record']);
    });
});
