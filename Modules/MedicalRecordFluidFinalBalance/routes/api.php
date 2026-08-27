<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordFluidFinalBalance\Http\Controllers\FluidFinalBalanceController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('fluid-final-balances', FluidFinalBalanceController::class)->only(['index', 'show'])->parameters(['fluid-final-balances' => 'record']);

    Route::apiResource('fluid-final-balances', FluidFinalBalanceController::class)->only(['store', 'update', 'destroy'])->parameters(['fluid-final-balances' => 'record']);
});
