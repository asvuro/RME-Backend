<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralReportType\Http\Controllers\ReportTypeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('report-types', ReportTypeController::class)->only(['index', 'show']);

    Route::apiResource('report-types', ReportTypeController::class)->only(['store', 'update', 'destroy']);
});
