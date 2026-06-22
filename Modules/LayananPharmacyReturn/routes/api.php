<?php

use Illuminate\Support\Facades\Route;
use Modules\LayananPharmacyReturn\Http\Controllers\PharmacyReturnController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('pharmacy-returns', PharmacyReturnController::class)->only(['index', 'show'])->parameters(['pharmacy-returns' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('pharmacy-returns', PharmacyReturnController::class)->only(['store', 'update', 'destroy'])->parameters(['pharmacy-returns' => 'record']);
    });
});
