<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralRadiologyRoom\Http\Controllers\GeneralRadiologyRoomController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('radiology-rooms', GeneralRadiologyRoomController::class)->only(['index', 'show'])->parameters(['radiology-rooms' => 'radiologyRoom']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('radiology-rooms', GeneralRadiologyRoomController::class)->only(['store', 'update', 'destroy'])->parameters(['radiology-rooms' => 'radiologyRoom']);
    });
});
