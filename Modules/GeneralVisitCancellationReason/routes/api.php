<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralVisitCancellationReason\Http\Controllers\VisitCancellationReasonController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('visit-cancellation-reasons', VisitCancellationReasonController::class)->only(['index', 'show']);

    Route::apiResource('visit-cancellation-reasons', VisitCancellationReasonController::class)->only(['store', 'update', 'destroy']);
});
