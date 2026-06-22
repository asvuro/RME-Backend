<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralPlanningPeriod\Http\Controllers\PlanningPeriodController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('planning-periods', PlanningPeriodController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('planning-periods', PlanningPeriodController::class)->only(['store', 'update', 'destroy']);
    });
});
