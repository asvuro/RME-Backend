<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralPackageTariffDistributionItem\Http\Controllers\PackageTariffDistributionItemController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('package-tariff-distribution-items', PackageTariffDistributionItemController::class)->only(['index', 'show'])->parameters(['package-tariff-distribution-items' => 'distribution_item']);

    Route::apiResource('package-tariff-distribution-items', PackageTariffDistributionItemController::class)->only(['store', 'update', 'destroy'])->parameters(['package-tariff-distribution-items' => 'distribution_item']);
});
