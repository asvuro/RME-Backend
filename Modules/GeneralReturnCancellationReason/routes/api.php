<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralReturnCancellationReason\Http\Controllers\ReturnCancellationReasonController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('return-cancellation-reasons', ReturnCancellationReasonController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('return-cancellation-reasons', ReturnCancellationReasonController::class)->only(['store', 'update', 'destroy']);
    });
});
