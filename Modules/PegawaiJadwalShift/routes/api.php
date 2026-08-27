<?php

use Illuminate\Support\Facades\Route;
use Modules\PegawaiJadwalShift\Http\Controllers\ShiftScheduleController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('shift-schedules', ShiftScheduleController::class)->only(['index', 'show']);
    Route::get('shift-schedules-by-ward', [ShiftScheduleController::class, 'byWardAndDateRange']);

    Route::apiResource('shift-schedules', ShiftScheduleController::class)->only(['store', 'update', 'destroy']);
});
