<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralOperatingRoom\Http\Controllers\GeneralOperatingRoomController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('operating-rooms', GeneralOperatingRoomController::class)->only(['index', 'show'])->parameters(['operating-rooms' => 'operatingRoom']);

    Route::apiResource('operating-rooms', GeneralOperatingRoomController::class)->only(['store', 'update', 'destroy'])->parameters(['operating-rooms' => 'operatingRoom']);
});
