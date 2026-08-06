<?php

use Illuminate\Support\Facades\Route;
use Modules\LayananLabAnalyzerOrder\Http\Controllers\LabAnalyzerOrderController;
use Modules\LayananLabAnalyzerOrder\Http\Controllers\LabAnalyzerVendorController;

/*
 * Parameter route: lab-analyzer-orders -> lab_analyzer_order (18 char, aman di
 * bawah batas 32 char param Symfony); endpoint transisi memakai {order} pendek.
 */
Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('lab-analyzer-vendors', LabAnalyzerVendorController::class)->only(['index', 'show']);
    Route::apiResource('lab-analyzer-orders', LabAnalyzerOrderController::class)->only(['index', 'show']);

    Route::middleware('role:petugas|admin')->group(function () {
        Route::apiResource('lab-analyzer-vendors', LabAnalyzerVendorController::class)->only(['store', 'update', 'destroy']);
        Route::apiResource('lab-analyzer-orders', LabAnalyzerOrderController::class)->only(['store', 'update', 'destroy']);

        // State machine LIS versi tracking (tanpa bridging HL7/ASTM):
        // ordered -> sent_to_analyzer -> result_received -> verified.
        Route::post('lab-analyzer-orders/{order}/send-to-analyzer', [LabAnalyzerOrderController::class, 'sendToAnalyzer']);
        Route::post('lab-analyzer-orders/{order}/result', [LabAnalyzerOrderController::class, 'recordResult']);
        Route::post('lab-analyzer-orders/{order}/verify', [LabAnalyzerOrderController::class, 'verify']);
    });
});
