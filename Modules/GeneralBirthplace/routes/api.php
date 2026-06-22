<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralBirthplace\Http\Controllers\BirthplaceController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('birthplaces', BirthplaceController::class)->only(['index', 'show'])->parameters(['birthplaces' => 'birthplace']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('birthplaces', BirthplaceController::class)->only(['store', 'update', 'destroy'])->parameters(['birthplaces' => 'birthplace']);
    });
});
