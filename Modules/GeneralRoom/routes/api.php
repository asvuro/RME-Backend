<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralRoom\Http\Controllers\RoomController;

// Gerbang peran role:petugas|admin lama sudah digantikan RoutePermissionGate
// global (RBAC dinamis, per-aksi) -- lihat rbac-dynamic-permission-plan.
Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('rooms', RoomController::class);
});
