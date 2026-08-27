<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralMixtureType\Http\Controllers\MixtureTypeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('mixture-types', MixtureTypeController::class)->only(['index', 'show']);

    Route::apiResource('mixture-types', MixtureTypeController::class)->only(['store', 'update', 'destroy']);
});
