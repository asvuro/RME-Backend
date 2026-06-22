<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralProfession\Http\Controllers\ProfessionController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('professions', ProfessionController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('professions', ProfessionController::class)->only(['store', 'update', 'destroy']);
    });
});
