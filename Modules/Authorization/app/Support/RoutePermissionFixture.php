<?php

namespace Modules\Authorization\Support;

use Illuminate\Support\Facades\DB;
use Modules\Authorization\Models\RoutePermission;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Sumber kebenaran TUNGGAL untuk data RBAC dinamis di luar tabel `route_permissions`
 * itu sendiri: satu fixture statis di-commit ke git, dibaca oleh:
 *  - RoleAndPermissionSeeder (buat Permission + grant baseline admin/petugas) --
 *    dipanggil di RIBUAN proses test, jadi HARUS cepat (baca file PHP, bukan scan rute).
 *  - RoutePermissionGate (peta controller_action -> permission saat request) --
 *    juga tidak boleh bergantung pada tabel route_permissions sudah ter-seed di
 *    proses test yang bersangkutan.
 *
 * Ditulis HANYA oleh SyncRoutePermissionsCommand (rbac:sync-permissions).
 */
class RoutePermissionFixture
{
    public static function path(): string
    {
        return base_path('database/seeders/data/route_permissions.php');
    }

    /** @return array<int, array{controller_action: string, permission: ?string, legacy_tier: string, is_public: bool}> */
    public static function load(): array
    {
        $path = self::path();

        if (! is_file($path)) {
            return [];
        }

        /** @var array<int, array{controller_action: string, permission: ?string, legacy_tier: string, is_public: bool}> $rows */
        $rows = require $path;

        return $rows;
    }

    /** @param array<int, array{controller_action: string, permission: ?string, legacy_tier: string, is_public: bool}> $rows */
    public static function write(array $rows): void
    {
        $export = var_export($rows, true);
        $generatedAt = now()->toDateTimeString();

        $contents = <<<PHP
<?php

// Fixture statis DIGENERATE oleh `php artisan rbac:sync-permissions` -- JANGAN
// disunting manual, akan tertimpa. Sumber kebenaran untuk RoleAndPermissionSeeder
// (grant baseline) dan RoutePermissionGate (peta izin saat request) -- keduanya
// TIDAK scan rute sendiri, cuma baca file ini supaya cepat di ribuan proses test.
// Generated: {$generatedAt}

return {$export};

PHP;

        file_put_contents(self::path(), $contents);
    }

    /**
     * Peta controller_action -> gerbang efektif, dipakai RoutePermissionGate.
     *
     * `requires_permission` sengaja FALSE untuk tier authenticated_any: rute
     * itu dulu HANYA digerbang auth:sanctum (tanpa role: sama sekali) --
     * siapa pun yang login lolos, terlepas role/permission apa pun yang ia
     * punya. Mewajibkan permission di sini adalah PENGETATAN baru yang tidak
     * diminta (bukan port perilaku lama) -- name permission tetap dicatat
     * sebagai metadata untuk kebutuhan RBAC granular di masa depan, tapi
     * tidak (belum) ditegakkan gerbangnya.
     *
     * @return array<string, array{permission: ?string, is_public: bool, requires_permission: bool}>
     */
    public static function map(): array
    {
        return collect(self::load())
            ->mapWithKeys(fn (array $row) => [
                $row['controller_action'] => [
                    'permission' => $row['permission'],
                    'is_public' => $row['is_public'],
                    'requires_permission' => ! in_array($row['legacy_tier'], [RoutePermission::TIER_PUBLIC, RoutePermission::TIER_AUTHENTICATED_ANY], true),
                ],
            ])
            ->all();
    }

    /**
     * Buat semua baris Permission + sinkronkan grant baseline admin (semua
     * non-public) & petugas (petugas_admin + authenticated_any) -- dipakai
     * SyncRoutePermissionsCommand DAN RoleAndPermissionSeeder supaya logika
     * grant tidak pernah drift antara keduanya.
     *
     * SENGAJA bulk-query mentah (bukan Permission::findOrCreate() /
     * Role::syncPermissions() per baris dari spatie): dengan ~2.900 permission,
     * pola per-baris berarti ribuan query per proses -- dipanggil di SETIAP
     * proses test (RoleAndPermissionSeeder), jadi harus O(1) query per tabel,
     * bukan O(n). Cache permission spatie di-flush sekali di akhir.
     */
    public static function syncPermissionsAndRoles(?array $rows = null): void
    {
        $rows ??= self::load();
        $guard = config('auth.defaults.guard');
        $now = now();

        $permissionNames = collect($rows)->pluck('permission')->filter()->unique()->values();

        if ($permissionNames->isNotEmpty()) {
            DB::table('permissions')->insertOrIgnore(
                $permissionNames->map(fn (string $name) => [
                    'name' => $name,
                    'guard_name' => $guard,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->all(),
            );
        }

        $admin = Role::findOrCreate('admin', $guard);
        $petugas = Role::findOrCreate('petugas', $guard);

        $permissionIdsByName = DB::table('permissions')
            ->where('guard_name', $guard)
            ->pluck('id', 'name');

        $adminGrantNames = collect($rows)->where('is_public', false)->pluck('permission')->filter()->unique();
        $petugasGrantNames = collect($rows)
            ->whereIn('legacy_tier', [RoutePermission::TIER_PETUGAS_ADMIN, RoutePermission::TIER_AUTHENTICATED_ANY])
            ->pluck('permission')->filter()->unique();

        self::replaceRolePermissions($admin->id, $adminGrantNames, $permissionIdsByName);
        self::replaceRolePermissions($petugas->id, $petugasGrantNames, $permissionIdsByName);

        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /** @param \Illuminate\Support\Collection<int, string> $permissionNames */
    protected static function replaceRolePermissions(int $roleId, $permissionNames, \Illuminate\Support\Collection $permissionIdsByName): void
    {
        DB::table('role_has_permissions')->where('role_id', $roleId)->delete();

        $pivotRows = $permissionNames
            ->map(fn (string $name) => $permissionIdsByName->get($name))
            ->filter()
            ->map(fn (int $permissionId) => ['permission_id' => $permissionId, 'role_id' => $roleId])
            ->values()
            ->all();

        if ($pivotRows !== []) {
            DB::table('role_has_permissions')->insertOrIgnore($pivotRows);
        }
    }
}
