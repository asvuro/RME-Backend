<?php

use Illuminate\Support\Facades\Route;
use Modules\PembatalanVisitCancellation\Http\Controllers\VisitCancellationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('pembatalan-visit-cancellations', VisitCancellationController::class)->only(['index', 'show'])->parameters(['pembatalan-visit-cancellations' => 'visit_cancellation']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('pembatalan-visit-cancellations', VisitCancellationController::class)->only(['store'])->parameters(['pembatalan-visit-cancellations' => 'visit_cancellation']);
    });
});
