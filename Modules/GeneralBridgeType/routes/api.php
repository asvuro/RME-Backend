<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralBridgeType\Http\Controllers\BridgeTypeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('bridge-types', BridgeTypeController::class);
});