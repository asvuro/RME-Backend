<?php

namespace Modules\Authorization\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Modules\Authorization\Models\RoutePermission;
use Modules\Authorization\Support\RoutePermissionFixture;

/**
 * Generator RBAC dinamis (fondasi #RBAC): scan SELURUH rute terdaftar,
 * turunkan satu Permission spatie per aksi (Controller@method), catat tier
 * akses lama (role:...) sebagai baseline grant admin/petugas, lalu tulis
 * ulang fixture statis database/seeders/data/route_permissions.php (lihat
 * RoutePermissionFixture -- sumber kebenaran TUNGGAL dipakai seeder & gerbang).
 *
 * Idempoten: aman dijalankan ulang tiap kali modul/rute baru ditambah.
 * Role kustom milik hospital (bukan admin/petugas) TIDAK PERNAH disentuh.
 */
class SyncRoutePermissionsCommand extends Command
{
    protected $signature = 'rbac:sync-permissions';

    protected $description = 'Generate Permission spatie dari seluruh rute terdaftar + tulis fixture seeder statis';

    public function handle(): int
    {
        $counts = [
            RoutePermission::TIER_PUBLIC => 0,
            RoutePermission::TIER_ADMIN_ONLY => 0,
            RoutePermission::TIER_PETUGAS_ADMIN => 0,
            RoutePermission::TIER_AUTHENTICATED_ANY => 0,
        ];

        // Rute yang SUDAH tercatat di fixture mempertahankan legacy_tier lama-nya
        // apa pun middleware live-nya sekarang. Middleware role:... yang dulu jadi
        // sinyal deteksi tier sudah dihapus total dari semua rute per migrasi RBAC
        // dinamis (RoutePermissionGate menggantikannya) -- scan live middleware
        // TIDAK BISA lagi membedakan admin_only/petugas_admin dari authenticated_any,
        // jadi re-derive di sini akan selalu menurunkan semua rute ke
        // authenticated_any (menonaktifkan gerbang izin diam-diam). Hanya rute BARU
        // (controller_action belum pernah ada di fixture) yang diklasifikasi dari
        // hasil analyzeRoute().
        $existingByAction = collect(RoutePermissionFixture::load())->keyBy('controller_action');

        $analyzed = [];
        foreach (app('router')->getRoutes() as $route) {
            /** @var Route $route */
            $controllerAction = RoutePermission::deriveControllerAction($route);
            $existing = $existingByAction->get($controllerAction);

            if ($existing !== null) {
                $method = $route->methods()[0] ?? 'GET';
                $row = [
                    'permission' => $existing['permission'],
                    'controller_action' => $controllerAction,
                    'method' => $method,
                    'uri' => $route->uri(),
                    'module' => $existing['is_public'] ? 'public' : $this->moduleFromAction($route->getActionName()),
                    'legacy_tier' => $existing['legacy_tier'],
                    'is_public' => $existing['is_public'],
                ];
            } else {
                $row = $this->analyzeRoute($route);
            }

            $counts[$row['legacy_tier']]++;
            $analyzed[] = $row;
        }

        $fixtureRows = array_map(fn (array $row) => [
            'controller_action' => $row['controller_action'],
            'permission' => $row['permission'],
            'legacy_tier' => $row['legacy_tier'],
            'is_public' => $row['is_public'],
        ], $analyzed);

        // Bulk-buat Permission + grant baseline DULU (bukan findOrCreate per
        // baris -- ~2.900 permission, harus O(1) query, lihat docblock
        // RoutePermissionFixture::syncPermissionsAndRoles).
        RoutePermissionFixture::syncPermissionsAndRoles($fixtureRows);

        $permissionIdsByName = \Illuminate\Support\Facades\DB::table('permissions')
            ->where('guard_name', config('auth.defaults.guard'))
            ->pluck('id', 'name');

        foreach ($analyzed as $row) {
            RoutePermission::updateOrCreate(
                ['controller_action' => $row['controller_action']],
                [
                    'permission_id' => $row['permission'] !== null ? $permissionIdsByName->get($row['permission']) : null,
                    'method' => $row['method'],
                    'uri' => $row['uri'],
                    'module' => $row['module'],
                    'legacy_tier' => $row['legacy_tier'],
                    'is_public' => $row['is_public'],
                ],
            );
        }

        RoutePermissionFixture::write($fixtureRows);

        Cache::forget('rbac:route-permission-map');

        $this->info(sprintf(
            'Sync selesai: %d public, %d admin_only, %d petugas_admin, %d authenticated_any.',
            $counts[RoutePermission::TIER_PUBLIC],
            $counts[RoutePermission::TIER_ADMIN_ONLY],
            $counts[RoutePermission::TIER_PETUGAS_ADMIN],
            $counts[RoutePermission::TIER_AUTHENTICATED_ANY],
        ));

        return self::SUCCESS;
    }

    protected function moduleFromAction(string $action): string
    {
        if (preg_match('/^Modules\\\\([A-Za-z0-9]+)\\\\Http\\\\Controllers\\\\/', $action, $m)) {
            return $m[1];
        }

        return 'system';
    }

    /**
     * @return array{permission: ?string, controller_action: string, method: string, uri: string, module: string, legacy_tier: string, is_public: bool}
     */
    protected function analyzeRoute(Route $route): array
    {
        $middleware = $route->gatherMiddleware();
        $method = $route->methods()[0] ?? 'GET';
        $uri = $route->uri();

        // gatherMiddleware() balik string alias mentah ('auth:sanctum',
        // 'role:petugas|admin'), BUKAN FQCN resolved seperti yang dirender
        // `route:list` -- jangan cocokkan ke Illuminate\Auth\Middleware\Authenticate.
        $hasAuth = collect($middleware)->contains(fn ($m) => $m === 'auth:sanctum' || str_starts_with($m, 'auth:'));
        $controllerAction = RoutePermission::deriveControllerAction($route);

        if (! $hasAuth) {
            return [
                'permission' => null,
                'controller_action' => $controllerAction,
                'method' => $method,
                'uri' => $uri,
                'module' => 'public',
                'legacy_tier' => RoutePermission::TIER_PUBLIC,
                'is_public' => true,
            ];
        }

        $action = $route->getActionName(); // "Modules\{Module}\Http\Controllers\{Controller}@{method}" (99.8% rute)

        if (preg_match('/^Modules\\\\([A-Za-z0-9]+)\\\\Http\\\\Controllers\\\\([A-Za-z0-9]+)Controller(?:@([A-Za-z0-9_]+))?$/', $action, $m)) {
            [$full, $module, $controller, $actionMethod] = array_pad($m, 4, null);
            $actionMethod ??= 'invoke'; // controller invokable (__invoke), tidak ada segmen @method
            $permissionName = Str::kebab($module).'.'.Str::kebab($controller).'.'.Str::kebab($actionMethod);
        } else {
            // Rute framework/non-modul yang tetap ber-auth (jarang) -- permission
            // fallback dari uri supaya tetap ada gerbang, bukan diam-diam tak terkelola.
            $permissionName = 'system.'.Str::kebab(str_replace(['/', '{', '}'], ['-', '', ''], $uri)).'.'.Str::kebab($method);
            $module = 'system';
        }

        $hasAdminOnly = in_array('role:admin', $middleware, true);
        $hasPetugasAdmin = in_array('role:petugas|admin', $middleware, true);

        $tier = match (true) {
            $hasAdminOnly => RoutePermission::TIER_ADMIN_ONLY,
            $hasPetugasAdmin => RoutePermission::TIER_PETUGAS_ADMIN,
            default => RoutePermission::TIER_AUTHENTICATED_ANY,
        };

        return [
            'permission' => $permissionName,
            'controller_action' => $controllerAction,
            'method' => $method,
            'uri' => $uri,
            'module' => $module,
            'legacy_tier' => $tier,
            'is_public' => false,
        ];
    }
}
