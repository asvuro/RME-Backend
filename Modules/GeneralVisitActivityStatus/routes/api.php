<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralVisitActivityStatus\Http\Controllers\VisitActivityStatusController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('visit-activity-statuses', VisitActivityStatusController::class);
});