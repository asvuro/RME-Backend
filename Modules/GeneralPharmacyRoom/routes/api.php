<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralPharmacyRoom\Http\Controllers\GeneralPharmacyRoomController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('pharmacy-rooms', GeneralPharmacyRoomController::class)->only(['index', 'show'])->parameters(['pharmacy-rooms' => 'pharmacyRoom']);

    Route::apiResource('pharmacy-rooms', GeneralPharmacyRoomController::class)->only(['store', 'update', 'destroy'])->parameters(['pharmacy-rooms' => 'pharmacyRoom']);
});
