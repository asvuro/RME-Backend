<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralPositionTitle\Http\Controllers\PositionTitleController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('position-titles', PositionTitleController::class)->only(['index', 'show']);

    Route::apiResource('position-titles', PositionTitleController::class)->only(['store', 'update', 'destroy']);
});
