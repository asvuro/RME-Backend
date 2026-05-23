<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralOtherStatus\Http\Controllers\OtherStatusController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('other-statuses', OtherStatusController::class);
});