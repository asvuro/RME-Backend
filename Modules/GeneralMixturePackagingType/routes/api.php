<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralMixturePackagingType\Http\Controllers\MixturePackagingTypeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('mixture-packaging-types', MixturePackagingTypeController::class);
});