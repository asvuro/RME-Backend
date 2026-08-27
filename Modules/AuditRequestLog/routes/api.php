<?php

use Illuminate\Support\Facades\Route;
use Modules\AuditRequestLog\Http\Controllers\RequestLogController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::get('request-logs', [RequestLogController::class, 'index']);
});
