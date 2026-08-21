<?php

use Illuminate\Support\Facades\Route;
use Modules\InventoryWardItemStock\Http\Controllers\InventoryWardItemStockController;

// Gerbang peran role:petugas|admin lama sudah digantikan RoutePermissionGate
// global (RBAC dinamis, per-aksi) -- lihat rbac-dynamic-permission-plan.
// WardScope::canAccessWard() (gerbang objek-level, di dalam controller)
// TIDAK terpengaruh sama sekali oleh perubahan ini.
Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('inventorywarditemstocks', InventoryWardItemStockController::class);
});
