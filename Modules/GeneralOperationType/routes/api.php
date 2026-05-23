<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralOperationType\Http\Controllers\OperationTypeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('operation-types', OperationTypeController::class);
});