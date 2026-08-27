<?php

use Illuminate\Support\Facades\Route;
use Modules\PenjaminRSClaimDriver\Http\Controllers\PenjaminRSClaimDriverController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('claim-drivers', PenjaminRSClaimDriverController::class)->only(['index', 'show'])->parameters(['claim-drivers' => 'claim_driver']);

    Route::apiResource('claim-drivers', PenjaminRSClaimDriverController::class)->only(['store', 'update', 'destroy'])->parameters(['claim-drivers' => 'claim_driver']);
});
