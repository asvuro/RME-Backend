<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralWardTransferRoute\Http\Controllers\WardTransferRouteController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('ward-transfer-routes', WardTransferRouteController::class)->only(['index', 'show'])->parameters(['ward-transfer-routes' => 'transfer_route']);

    Route::apiResource('ward-transfer-routes', WardTransferRouteController::class)->only(['store', 'update', 'destroy'])->parameters(['ward-transfer-routes' => 'transfer_route']);
});
