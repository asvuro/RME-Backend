<?php

namespace Modules\Authorization\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Bukti RBAC dinamis (migrasi pilot #Authorization): gerbang role:admin lama
 * sudah dihapus dari routes/api.php, digantikan RoutePermissionGate global.
 * Role kustom (bukan admin/petugas) TETAP tidak bisa CRUD role/permission
 * kecuali diberi permission authorization.*-nya secara eksplisit.
 */
class RoutePermissionGateAdminOnlyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_role_kustom_tanpa_permission_tidak_bisa_membuat_role(): void
    {
        $role = Role::create(['name' => 'supervisor', 'guard_name' => config('auth.defaults.guard')]);
        $user = User::factory()->create();
        $user->assignRole($role);
        $this->actingAs($user, 'sanctum');

        $this->postJson('/api/v1/roles', ['name' => 'coba', 'permissions' => []])
            ->assertStatus(403);
    }

    public function test_role_kustom_dengan_permission_bisa_membuat_role(): void
    {
        $role = Role::create(['name' => 'supervisor', 'guard_name' => config('auth.defaults.guard')]);
        $role->givePermissionTo('authorization.role.store');
        $user = User::factory()->create();
        $user->assignRole($role);
        $this->actingAs($user, 'sanctum');

        $this->postJson('/api/v1/roles', ['name' => 'coba', 'permissions' => []])
            ->assertCreated();
    }

    public function test_petugas_tetap_tidak_bisa_kelola_role_persis_seperti_sebelum_migrasi(): void
    {
        $user = User::factory()->create();
        $user->assignRole('petugas');
        $this->actingAs($user, 'sanctum');

        $this->postJson('/api/v1/roles', ['name' => 'coba', 'permissions' => []])
            ->assertStatus(403);
    }
}
