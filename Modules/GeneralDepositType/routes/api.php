<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralDepositType\Http\Controllers\DepositTypeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('deposit-types', DepositTypeController::class);
});