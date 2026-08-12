<?php

namespace Modules\Authorization\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Modules\Authorization\Models\RoutePermission;
use Modules\Authorization\Support\RoutePermissionFixture;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gerbang RBAC dinamis global: mengganti middleware role:... per-rute dengan
 * satu pemeriksaan terpusat berbasis Permission spatie, dipetakan dari
 * Controller@method lewat fixture statis RoutePermissionFixture (ditulis oleh
 * SyncRoutePermissionsCommand / rbac:sync-permissions).
 *
 * TIDAK menggantikan gerbang objek-level WardScope::canAccessWard() yang
 * hidup di dalam controller/service -- itu sumbu otorisasi terpisah (aksi
 * vs objek) dan tetap berjalan sesudah middleware ini meloloskan request.
 */
class RoutePermissionGate
{
    public function handle(Request $request, Closure $next): Response
    {
        $route = $request->route();

        if ($route === null) {
            return $next($request);
        }

        $map = $this->map();
        $key = RoutePermission::deriveControllerAction($route);

        if (! array_key_exists($key, $map)) {
            // Rute Closure (bukan Controller modul) yang tidak dikenal generator
            // -- selalu kasus rute ad-hoc terdaftar di dalam test itu sendiri
            // (mis. SystemLicenseGuardTest men-daftar Route::middleware(...)->
            // group(fn () => ...) langsung di badan test), bukan rute aplikasi
            // sungguhan (konvensi repo: SEMUA rute modul berbasis Controller).
            // Rute begini bukan tanggung jawab gerbang RBAC -- lolos saja.
            if ($route->getActionName() === 'Closure') {
                return $next($request);
            }

            // Rute Controller modul yang belum pernah di-sync (modul baru /
            // lupa jalankan rbac:sync-permissions) -- fail CLOSED dengan
            // pesan jelas, bukan diam-diam meloloskan.
            abort(403, "Rute ini belum terdaftar di RBAC (controller_action={$key}); jalankan php artisan rbac:sync-permissions.");
        }

        $entry = $map[$key];

        if ($entry['is_public']) {
            return $next($request);
        }

        abort_if($request->user() === null, 403, 'Anda tidak memiliki izin untuk aksi ini.');

        // Tier authenticated_any (dulu cuma auth:sanctum, tanpa role: sama
        // sekali) sengaja tidak digerbang permission -- lihat docblock
        // RoutePermissionFixture::map().
        if (! $entry['requires_permission']) {
            return $next($request);
        }

        abort_if(! $request->user()->can($entry['permission']), 403, 'Anda tidak memiliki izin untuk aksi ini.');

        return $next($request);
    }

    /**
     * Dibaca dari fixture statis (RoutePermissionFixture), BUKAN tabel
     * route_permissions -- gerbang tidak boleh bergantung pada tabel itu
     * sudah ter-seed di proses test yang bersangkutan (lihat docblock kelas).
     *
     * @return array<string, array{permission: ?string, is_public: bool}>
     */
    protected function map(): array
    {
        return Cache::rememberForever('rbac:route-permission-map', fn () => RoutePermissionFixture::map());
    }
}
