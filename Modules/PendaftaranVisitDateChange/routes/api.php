<?php

use Illuminate\Support\Facades\Route;
use Modules\PendaftaranVisitDateChange\Http\Controllers\VisitDateChangeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('visitdatechanges', VisitDateChangeController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('visitdatechanges', VisitDateChangeController::class)->only(['store']);
    });
});
