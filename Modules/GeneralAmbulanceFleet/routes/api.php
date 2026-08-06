<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralAmbulanceFleet\Http\Controllers\AmbulanceController;
use Modules\GeneralAmbulanceFleet\Http\Controllers\AmbulanceTripController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('ambulances', AmbulanceController::class)->only(['index', 'show']);

    Route::apiResource('ambulance-trips', AmbulanceTripController::class)
        ->only(['index', 'show'])
        ->parameters(['ambulance-trips' => 'trip']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('ambulances', AmbulanceController::class)->only(['store', 'update']);
        // destroy ambulans tidak ada: cascade ke riwayat trip (lihat docblock controller).

        Route::apiResource('ambulance-trips', AmbulanceTripController::class)
            ->only(['store', 'update'])
            ->parameters(['ambulance-trips' => 'trip']);
        // destroy trip tidak ada: jejak operasional.

        Route::post('ambulance-trips/{trip}/complete', [AmbulanceTripController::class, 'complete']);
    });
});
