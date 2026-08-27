<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralTariffType\Http\Controllers\TariffTypeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('tariff-types', TariffTypeController::class)->only(['index', 'show']);

    Route::apiResource('tariff-types', TariffTypeController::class)->only(['store', 'update', 'destroy']);
});
