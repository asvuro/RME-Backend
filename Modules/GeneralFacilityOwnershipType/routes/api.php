<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralFacilityOwnershipType\Http\Controllers\FacilityOwnershipTypeController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('facility-ownership-types', FacilityOwnershipTypeController::class);
});