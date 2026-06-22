<?php

use Illuminate\Support\Facades\Route;
use Modules\BerkasKlaimRadiologyClaimItem\Http\Controllers\RadiologyClaimItemController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('radiology-claim-items', RadiologyClaimItemController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('radiology-claim-items', RadiologyClaimItemController::class)->only(['store']);
    });
});
