<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralCardType\Http\Controllers\CardTypeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('card-types', CardTypeController::class)->only(['index', 'show']);

    Route::apiResource('card-types', CardTypeController::class)->only(['store', 'update', 'destroy']);
});
