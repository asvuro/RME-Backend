<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Authorization\Support\RoutePermissionFixture;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Role dasar aplikasi + permission RBAC dinamis. Guard mengikuti guard
     * aktif aplikasi (sanctum). Permission dibaca dari fixture statis
     * RoutePermissionFixture (BUKAN scan rute -- dipanggil di ribuan proses
     * test, harus cepat); admin dapat semua permission non-public, petugas
     * dapat permission tier petugas_admin+authenticated_any (meniru gerbang
     * role:petugas|admin lama persis, supaya cutover tidak mengubah
     * perilaku test/user yang ada).
     */
    public function run(): void
    {
        $guard = config('auth.defaults.guard');

        foreach (['admin', 'petugas'] as $role) {
            Role::findOrCreate($role, $guard);
        }

        RoutePermissionFixture::syncPermissionsAndRoles();
    }
}
