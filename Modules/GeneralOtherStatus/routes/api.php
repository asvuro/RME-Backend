<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralOtherStatus\Http\Controllers\OtherStatusController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('other-statuses', OtherStatusController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('other-statuses', OtherStatusController::class)->only(['store', 'update', 'destroy']);
    });
});
