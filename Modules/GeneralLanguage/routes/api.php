<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralLanguage\Http\Controllers\LanguageController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('languages', LanguageController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('languages', LanguageController::class)->only(['store', 'update', 'destroy']);
    });
});
