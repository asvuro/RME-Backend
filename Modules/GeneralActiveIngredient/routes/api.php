<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralActiveIngredient\Http\Controllers\ActiveIngredientController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('active-ingredients', ActiveIngredientController::class)->only(['index', 'show']);

    Route::apiResource('active-ingredients', ActiveIngredientController::class)->only(['store', 'update', 'destroy']);
});
