<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralWardTariff\Http\Controllers\WardTariffController;

// Gerbang peran role:petugas|admin lama sudah digantikan RoutePermissionGate
// global (RBAC dinamis, per-aksi) -- lihat rbac-dynamic-permission-plan.
Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('ward-tariffs', WardTariffController::class);
});
