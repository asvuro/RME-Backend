<?php

use Illuminate\Support\Facades\Route;
use Modules\InventoryBloodBag\Http\Controllers\BloodBagController;
use Modules\InventoryBloodBag\Http\Controllers\CrossmatchTestController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('blood-bags', BloodBagController::class)->only(['index', 'show']);
    Route::apiResource('crossmatch-tests', CrossmatchTestController::class)->only(['index', 'show']);

    Route::apiResource('blood-bags', BloodBagController::class)->only(['store', 'update', 'destroy']);
    Route::post('blood-bags/{blood_bag}/crossmatch', [BloodBagController::class, 'crossmatch']);
    Route::post('blood-bags/{blood_bag}/transfuse', [BloodBagController::class, 'transfuse']);
    Route::post('crossmatch-tests/{crossmatch_test}/release', [CrossmatchTestController::class, 'release']);
});
