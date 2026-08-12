<?php

namespace Modules\Authorization\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class PermissionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }

    private function actingUser(): User
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $this->actingAs($user, 'sanctum');

        return $user;
    }

    public function test_it_creates_and_lists_permissions(): void
    {
        $this->actingUser();

        // RBAC dinamis (rbac:sync-permissions) sudah men-seed ~2.900 permission
        // rute nyata lewat RoleAndPermissionSeeder -- daftar TIDAK kosong sejak
        // awal (index() paginate(15) urut id, permission baru ID-nya paling
        // besar sehingga tidak tentu muncul di halaman 1). Cek persistensi
        // lewat DB langsung + list tetap 200 dengan meta paginasi masuk akal.
        $this->postJson('/api/v1/permissions', ['name' => 'edit-pasien'])->assertCreated();

        $this->assertDatabaseHas('permissions', ['name' => 'edit-pasien']);

        $response = $this->getJson('/api/v1/permissions');

        $response->assertOk()->assertJsonCount(15, 'data');
    }

    public function test_it_rejects_permission_with_duplicate_name(): void
    {
        $this->actingUser();
        Permission::create(['name' => 'edit-pasien']);

        $response = $this->postJson('/api/v1/permissions', ['name' => 'edit-pasien']);

        $response->assertStatus(422)->assertJsonValidationErrors('name');
    }

    public function test_it_deletes_permission(): void
    {
        $this->actingUser();
        $permission = Permission::create(['name' => 'edit-pasien']);

        $response = $this->deleteJson("/api/v1/permissions/{$permission->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('permissions', ['id' => $permission->id]);
    }
}
