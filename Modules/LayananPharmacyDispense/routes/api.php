<?php

use Illuminate\Support\Facades\Route;
use Modules\LayananPharmacyDispense\Http\Controllers\DispenseController;
use Modules\LayananPharmacyDispense\Http\Controllers\PharmacyDispenseController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('pharmacy-dispenses', PharmacyDispenseController::class)->only(['index', 'store', 'show', 'update'])->parameters(['pharmacy-dispenses' => 'dispense']);

    // Pelayanan resep end-to-end (#10): telaah → restriksi → stok → tagihan.
    Route::post('prescriptions/{prescription}/dispense', [DispenseController::class, 'store']);
});
