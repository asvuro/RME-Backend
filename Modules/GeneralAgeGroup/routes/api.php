<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralAgeGroup\Http\Controllers\AgeGroupController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('age-groups', AgeGroupController::class)->only(['index', 'show']);

    Route::apiResource('age-groups', AgeGroupController::class)->only(['store', 'update', 'destroy']);
});
