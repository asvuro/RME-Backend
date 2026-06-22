<?php

use Illuminate\Support\Facades\Route;
use Modules\PendaftaranWardQueue\Http\Controllers\WardQueueController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('ward-queues', WardQueueController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('ward-queues', WardQueueController::class)->only(['store', 'update']);
    });
});
