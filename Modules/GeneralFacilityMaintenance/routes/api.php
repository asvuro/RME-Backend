<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralFacilityMaintenance\Http\Controllers\MaintenanceAssetController;
use Modules\GeneralFacilityMaintenance\Http\Controllers\MaintenanceWorkOrderController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('maintenance-assets', MaintenanceAssetController::class)
        ->parameters(['maintenance-assets' => 'maintenance_asset'])
        ->only(['index', 'show']);

    Route::apiResource('work-orders', MaintenanceWorkOrderController::class)
        ->parameters(['work-orders' => 'wo'])
        ->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('maintenance-assets', MaintenanceAssetController::class)
            ->parameters(['maintenance-assets' => 'maintenance_asset'])
            ->only(['store', 'update', 'destroy']);

        Route::apiResource('work-orders', MaintenanceWorkOrderController::class)
            ->parameters(['work-orders' => 'wo'])
            ->only(['store', 'update', 'destroy']);

        Route::post('work-orders/{wo}/assign', [MaintenanceWorkOrderController::class, 'assign']);
        Route::post('work-orders/{wo}/complete', [MaintenanceWorkOrderController::class, 'complete']);
    });
});
