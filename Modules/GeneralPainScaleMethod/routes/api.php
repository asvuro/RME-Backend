<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralPainScaleMethod\Http\Controllers\PainScaleMethodController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('pain-scale-methods', PainScaleMethodController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('pain-scale-methods', PainScaleMethodController::class)->only(['store', 'update', 'destroy']);
    });
});
