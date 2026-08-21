<?php

use Illuminate\Support\Facades\Route;
use Modules\Authorization\Http\Controllers\PermissionController;
use Modules\Authorization\Http\Controllers\RoleController;
use Modules\Authorization\Http\Controllers\UserRoleController;

// Gerbang peran role:admin lama sudah digantikan RoutePermissionGate global
// (RBAC dinamis, per-aksi) -- lihat rbac-dynamic-permission-plan.
Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('roles', RoleController::class);

    Route::apiResource('permissions', PermissionController::class)->only(['index', 'store', 'destroy']);

    Route::get('users/{user}/roles', [UserRoleController::class, 'index']);
    Route::put('users/{user}/roles', [UserRoleController::class, 'sync']);
});
