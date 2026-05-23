<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralEmploymentStatus\Http\Controllers\EmploymentStatusController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('employment-statuses', EmploymentStatusController::class);
});