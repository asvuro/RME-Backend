<?php

use Illuminate\Support\Facades\Route;
use Modules\PendaftaranQueueCall\Http\Controllers\QueueCallController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('queue-calls', QueueCallController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('queue-calls', QueueCallController::class)->only(['store']);
    });
});
