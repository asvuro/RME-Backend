<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralReligion\Http\Controllers\ReligionController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('religions', ReligionController::class)->only(['index', 'show']);

    Route::apiResource('religions', ReligionController::class)->only(['store', 'update', 'destroy']);
});
