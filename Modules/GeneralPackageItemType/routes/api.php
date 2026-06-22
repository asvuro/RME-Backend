<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralPackageItemType\Http\Controllers\PackageItemTypeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('package-item-types', PackageItemTypeController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('package-item-types', PackageItemTypeController::class)->only(['store', 'update', 'destroy']);
    });
});
