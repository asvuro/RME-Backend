<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralMonthName\Http\Controllers\MonthNameController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('month-names', MonthNameController::class)->only(['index', 'show']);

    Route::apiResource('month-names', MonthNameController::class)->only(['store', 'update', 'destroy']);
});
