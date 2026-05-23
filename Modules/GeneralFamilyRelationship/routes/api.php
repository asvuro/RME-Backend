<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralFamilyRelationship\Http\Controllers\FamilyRelationshipController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('family-relationships', FamilyRelationshipController::class);
});