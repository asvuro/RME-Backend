<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralWardService\Http\Controllers\WardServiceController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('ward-services', WardServiceController::class)->only(['index', 'show'])->parameters(['ward-services' => 'ward_service']);

    Route::apiResource('ward-services', WardServiceController::class)->only(['store', 'update', 'destroy'])->parameters(['ward-services' => 'ward_service']);
});
