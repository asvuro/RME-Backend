<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralPackageItem\Http\Controllers\PackageItemController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('package-items', PackageItemController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('package-items', PackageItemController::class)->only(['store', 'update', 'destroy']);
    });
});
