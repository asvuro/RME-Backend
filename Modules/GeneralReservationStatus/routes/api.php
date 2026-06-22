<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralReservationStatus\Http\Controllers\ReservationStatusController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('reservation-statuses', ReservationStatusController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('reservation-statuses', ReservationStatusController::class)->only(['store', 'update', 'destroy']);
    });
});
