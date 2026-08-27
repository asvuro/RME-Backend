<?php

use Illuminate\Support\Facades\Route;
use Modules\PembayaranPackageInvoiceItem\Http\Controllers\PackageInvoiceItemController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('package-invoice-items', PackageInvoiceItemController::class)->only(['index', 'show'])->parameters(['package-invoice-items' => 'package_invoice_item']);

    Route::apiResource('package-invoice-items', PackageInvoiceItemController::class)->only(['store', 'update', 'destroy'])->parameters(['package-invoice-items' => 'package_invoice_item']);
});
