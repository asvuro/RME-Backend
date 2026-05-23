<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralPayrollDeduction\Http\Controllers\PayrollDeductionController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('payroll-deductions', PayrollDeductionController::class);
});