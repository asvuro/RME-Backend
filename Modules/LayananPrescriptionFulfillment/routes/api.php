<?php

use Illuminate\Support\Facades\Route;
use Modules\LayananPrescriptionFulfillment\Http\Controllers\PrescriptionFulfillmentController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('prescription-fulfillments', PrescriptionFulfillmentController::class)->only(['index', 'show'])->parameters(['prescription-fulfillments' => 'record']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('prescription-fulfillments', PrescriptionFulfillmentController::class)->only(['store', 'update', 'destroy'])->parameters(['prescription-fulfillments' => 'record']);
    });
});
