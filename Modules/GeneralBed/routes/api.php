<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralBed\Http\Controllers\BedController;

// Gerbang peran role:petugas|admin lama sudah digantikan RoutePermissionGate
// global (RBAC dinamis, per-aksi) -- lihat rbac-dynamic-permission-plan.
Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('beds', BedController::class);
    Route::post('beds/{bed}/reserve', [BedController::class, 'reserve']);
    Route::post('beds/{bed}/release-reservation', [BedController::class, 'releaseReservation']);
});
