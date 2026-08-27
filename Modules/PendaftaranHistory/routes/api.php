<?php

use Illuminate\Support\Facades\Route;
use Modules\PendaftaranHistory\Http\Controllers\PendaftaranHistoryController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('registration-histories', PendaftaranHistoryController::class)->only(['index', 'show'])->parameters(['registration-histories' => 'history']);

    Route::apiResource('registration-histories', PendaftaranHistoryController::class)->only(['store'])->parameters(['registration-histories' => 'history']);
});
