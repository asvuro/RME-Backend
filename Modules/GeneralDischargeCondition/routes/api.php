<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralDischargeCondition\Http\Controllers\DischargeConditionController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('discharge-conditions', DischargeConditionController::class);
});