<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralPayrollAddition\Http\Controllers\PayrollAdditionController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('payroll-additions', PayrollAdditionController::class);
});