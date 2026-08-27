<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralOtherService\Http\Controllers\OtherServiceController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('other-services', OtherServiceController::class)->only(['index', 'show']);

    Route::apiResource('other-services', OtherServiceController::class)->only(['store', 'update', 'destroy']);
});
