<?php

namespace Modules\Authorization\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\Authorization\Models\RoutePermission;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MyAccessControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }

    private function registerRoute(string $module, string $action, string $legacyTier, ?string $permissionName): void
    {
        $permissionId = null;
        if ($permissionName !== null) {
            $permission = Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => config('auth.defaults.guard')]);
            $permissionId = $permission->id;
        }

        RoutePermission::create([
            'permission_id' => $permissionId,
            'method' => 'GET',
            'uri' => strtolower($module).'/'.strtolower($action),
            'controller_action' => "Modules\\{$module}\\Http\\Controllers\\{$action}Controller@index",
            'module' => $module,
            'legacy_tier' => $legacyTier,
            'is_public' => $legacyTier === RoutePermission::TIER_PUBLIC,
        ]);
    }

    public function test_it_selalu_menyertakan_permission_tier_authenticated_any(): void
    {
        $this->registerRoute('AplikasiSetting', 'RsSetting', RoutePermission::TIER_AUTHENTICATED_ANY, 'aplikasi-setting.rs-setting.index');

        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $response = $this->getJson('/api/v1/me/modules');

        $response->assertOk();
        $response->assertJsonPath('data.modules', ['AplikasiSetting']);
        $response->assertJsonPath('data.permissions_by_module.AplikasiSetting', ['aplikasi-setting.rs-setting.index']);
    }

    public function test_modul_admin_only_hanya_muncul_untuk_user_yang_punya_permission(): void
    {
        $this->registerRoute('Authorization', 'Role', RoutePermission::TIER_ADMIN_ONLY, 'authorization.role.store');

        $tanpaIzin = User::factory()->create();
        $this->actingAs($tanpaIzin, 'sanctum');
        $this->getJson('/api/v1/me/modules')->assertJsonPath('data.modules', []);

        $role = Role::create(['name' => 'supervisor', 'guard_name' => config('auth.defaults.guard')]);
        $role->givePermissionTo('authorization.role.store');
        $adminUser = User::factory()->create();
        $adminUser->assignRole($role);
        $this->actingAs($adminUser, 'sanctum');

        $response = $this->getJson('/api/v1/me/modules');
        $response->assertJsonPath('data.modules', ['Authorization']);
        $response->assertJsonPath('data.permissions_by_module.Authorization', ['authorization.role.store']);
    }

    public function test_rute_public_tidak_muncul_di_daftar_modul(): void
    {
        $this->registerRoute('System', 'Up', RoutePermission::TIER_PUBLIC, null);

        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $this->getJson('/api/v1/me/modules')->assertJsonPath('data.modules', []);
    }
}
