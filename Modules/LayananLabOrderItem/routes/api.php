<?php

use Illuminate\Support\Facades\Route;
use Modules\LayananLabOrderItem\Http\Controllers\LabOrderItemController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('lab-order-items', LabOrderItemController::class)->only(['index', 'show'])->parameters(['lab-order-items' => 'lab_order_item']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('lab-order-items', LabOrderItemController::class)->only(['store'])->parameters(['lab-order-items' => 'lab_order_item']);
    });
});
