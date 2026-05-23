<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralReturnCancellationReason\Http\Controllers\ReturnCancellationReasonController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('return-cancellation-reasons', ReturnCancellationReasonController::class);
});