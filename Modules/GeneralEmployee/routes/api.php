<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralEmployee\Http\Controllers\EmployeeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('employees', EmployeeController::class)->only(['index', 'show']);

    Route::apiResource('employees', EmployeeController::class)->only(['store', 'update', 'destroy']);
});
