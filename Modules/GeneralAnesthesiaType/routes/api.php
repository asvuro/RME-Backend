<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralAnesthesiaType\Http\Controllers\AnesthesiaTypeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('anesthesia-types', AnesthesiaTypeController::class)->only(['index', 'show']);

    Route::apiResource('anesthesia-types', AnesthesiaTypeController::class)->only(['store', 'update', 'destroy']);
});
