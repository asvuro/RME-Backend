<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralReturnCancellationType\Http\Controllers\ReturnCancellationTypeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('return-cancellation-types', ReturnCancellationTypeController::class);
});