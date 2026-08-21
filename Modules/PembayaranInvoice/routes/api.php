<?php

use Illuminate\Support\Facades\Route;
use Modules\PembayaranInvoice\Http\Controllers\InvoiceController;
use Modules\PembayaranInvoice\Http\Controllers\InvoiceGuarantorController;

// Gerbang peran role:petugas|admin/role:admin lama sudah digantikan
// RoutePermissionGate global (RBAC dinamis, per-aksi) -- lihat
// rbac-dynamic-permission-plan. invoices.unlock tetap admin-only lewat
// permission (legacy_tier admin_only, cuma role admin yang di-grant baseline).
Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('invoices', InvoiceController::class);

    // Distribusi penjamin (port pembayaran.penjamin_tagihan simgos2).
    Route::get('invoices/{invoice}/guarantors', [InvoiceGuarantorController::class, 'index']);
    Route::get('invoices/{invoice}/coverage', [InvoiceGuarantorController::class, 'coverage']);
    Route::post('invoices/{invoice}/guarantors', [InvoiceGuarantorController::class, 'store']);
    Route::post('invoices/{invoice}/redistribute', [InvoiceGuarantorController::class, 'redistribute']);

    // Kasir menutup tagihan; pembukaan hanya admin.
    Route::post('invoices/{invoice}/lock', [InvoiceGuarantorController::class, 'lock']);
    Route::post('invoices/{invoice}/unlock', [InvoiceGuarantorController::class, 'unlock']);
});
