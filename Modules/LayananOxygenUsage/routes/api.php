<?php

use Illuminate\Support\Facades\Route;
use Modules\LayananOxygenUsage\Http\Controllers\OxygenUsageController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('oxygen-usages', OxygenUsageController::class)->only(['index', 'show'])->parameters(['oxygen-usages' => 'oxygen_usage']);

    Route::apiResource('oxygen-usages', OxygenUsageController::class)->only(['store', 'update'])->parameters(['oxygen-usages' => 'oxygen_usage']);
});
