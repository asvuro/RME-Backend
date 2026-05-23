<?php

use Illuminate\Support\Facades\Route;
use Modules\PembayaranInvoice\Http\Controllers\InvoiceController;
use Modules\PembayaranInvoice\Http\Controllers\InvoiceGuarantorController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('invoices', InvoiceController::class);

    // Distribusi penjamin (port pembayaran.penjamin_tagihan simgos2).
    Route::get('invoices/{invoice}/guarantors', [InvoiceGuarantorController::class, 'index']);
    Route::post('invoices/{invoice}/guarantors', [InvoiceGuarantorController::class, 'store']);
    Route::post('invoices/{invoice}/redistribute', [InvoiceGuarantorController::class, 'redistribute']);
    Route::get('invoices/{invoice}/coverage', [InvoiceGuarantorController::class, 'coverage']);

    // Kasir menutup/membuka tagihan; pembukaan hanya admin.
    Route::post('invoices/{invoice}/lock', [InvoiceGuarantorController::class, 'lock']);
    Route::middleware('role:admin')->group(function () {
        Route::post('invoices/{invoice}/unlock', [InvoiceGuarantorController::class, 'unlock']);
    });
});
