<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralHealthProviderType\Http\Controllers\HealthProviderTypeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('health-provider-types', HealthProviderTypeController::class);
});