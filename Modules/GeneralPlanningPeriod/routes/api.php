<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralPlanningPeriod\Http\Controllers\PlanningPeriodController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('planning-periods', PlanningPeriodController::class);
});