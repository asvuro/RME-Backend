<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralYesNoOption\Http\Controllers\YesNoOptionController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('yes-no-options', YesNoOptionController::class)->only(['index', 'show']);

    Route::apiResource('yes-no-options', YesNoOptionController::class)->only(['store', 'update', 'destroy']);
});
