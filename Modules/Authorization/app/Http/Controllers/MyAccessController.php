<?php

namespace Modules\Authorization\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Authorization\Models\RoutePermission;

class MyAccessController extends Controller
{
    /**
     * Modul & izin efektif milik user yang sedang login, dipakai frontend
     * untuk bangun menu (backend sudah menyembunyikan modul non-aktif lewat
     * 404 alami nwidart/laravel-modules -- ini melengkapi sisi RBAC: modul
     * AKTIF mana yang boleh dibuka user ini, dan aksi apa saja di dalamnya).
     *
     * Tier authenticated_any TIDAK digerbang permission oleh RoutePermissionGate
     * (siapa pun login lolos) -- disertakan otomatis di sini apa pun permission
     * milik user, supaya konsisten dengan perilaku gerbang sesungguhnya.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $userPermissionNames = $user->getAllPermissions()->pluck('name')->flip();

        $rows = RoutePermission::query()
            ->where('is_public', false)
            ->whereNotNull('permission_id')
            ->with('permission:id,name')
            ->get(['module', 'permission_id', 'legacy_tier']);

        $permissionsByModule = $rows
            ->filter(fn (RoutePermission $row) => $row->legacy_tier === RoutePermission::TIER_AUTHENTICATED_ANY
                || $userPermissionNames->has($row->permission?->name))
            ->groupBy('module')
            ->map(fn ($group) => $group->pluck('permission.name')->filter()->unique()->sort()->values())
            ->sortKeys();

        return response()->json([
            'success' => true,
            'data' => [
                'modules' => $permissionsByModule->keys()->values(),
                'permissions_by_module' => $permissionsByModule,
            ],
        ]);
    }
}
