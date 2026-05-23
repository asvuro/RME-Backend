<?php

use Illuminate\Support\Facades\Route;
use Modules\AuditActivityLog\Http\Controllers\ActivityLogController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::middleware('role:admin')->group(function () {
        Route::get('activity-logs', [ActivityLogController::class, 'index']);
    });
});
