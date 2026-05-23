<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralOperationGroup\Http\Controllers\OperationGroupController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('operation-groups', OperationGroupController::class);
});