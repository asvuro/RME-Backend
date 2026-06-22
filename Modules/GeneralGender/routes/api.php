<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralGender\Http\Controllers\GenderController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('genders', GenderController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('genders', GenderController::class)->only(['store', 'update', 'destroy']);
    });
});
