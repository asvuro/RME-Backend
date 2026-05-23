<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralVisitStatus\Http\Controllers\VisitStatusController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('visit-statuses', VisitStatusController::class);
});