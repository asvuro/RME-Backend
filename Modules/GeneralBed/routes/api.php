<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralBed\Http\Controllers\BedController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('beds', BedController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('beds', BedController::class)->only(['store', 'update', 'destroy']);
        Route::post('beds/{bed}/reserve', [BedController::class, 'reserve']);
        Route::post('beds/{bed}/release-reservation', [BedController::class, 'releaseReservation']);
    });
});
