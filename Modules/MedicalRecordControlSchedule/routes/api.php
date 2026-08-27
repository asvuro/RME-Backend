<?php

use Illuminate\Support\Facades\Route;
use Modules\MedicalRecordControlSchedule\Http\Controllers\ControlScheduleController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('control-schedules', ControlScheduleController::class)->only(['index', 'show'])->parameters(['control-schedules' => 'record']);

    Route::apiResource('control-schedules', ControlScheduleController::class)->only(['store', 'update', 'destroy'])->parameters(['control-schedules' => 'record']);
});
