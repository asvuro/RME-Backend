<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralPrintType\Http\Controllers\PrintTypeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('print-types', PrintTypeController::class)->only(['index', 'show']);

    Route::apiResource('print-types', PrintTypeController::class)->only(['store', 'update', 'destroy']);
});
