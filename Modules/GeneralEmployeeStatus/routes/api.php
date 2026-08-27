<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralEmployeeStatus\Http\Controllers\EmployeeStatusController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('employee-statuses', EmployeeStatusController::class)->only(['index', 'show']);

    Route::apiResource('employee-statuses', EmployeeStatusController::class)->only(['store', 'update', 'destroy']);
});
