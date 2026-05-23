<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Role dasar aplikasi. Guard mengikuti guard aktif aplikasi (sanctum).
     */
    public function run(): void
    {
        $guard = config('auth.defaults.guard');

        foreach (['admin', 'petugas'] as $role) {
            Role::findOrCreate($role, $guard);
        }
    }
}
