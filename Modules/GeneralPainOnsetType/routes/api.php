<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralPainOnsetType\Http\Controllers\PainOnsetTypeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('pain-onset-types', PainOnsetTypeController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('pain-onset-types', PainOnsetTypeController::class)->only(['store', 'update', 'destroy']);
    });
});
