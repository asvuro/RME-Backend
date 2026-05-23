<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralFlow\Http\Controllers\FlowController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('flows', FlowController::class);
});