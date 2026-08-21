<?php

namespace Modules\GeneralBed\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralBed\Models\Bed;
use Modules\GeneralRoom\Models\Room;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Bukti RBAC dinamis (fondasi + migrasi pilot #GeneralBed): role KUSTOM
 * (bukan admin/petugas) hanya bisa store bed kalau diberi permission
 * general-bed.bed.store secara eksplisit -- gerbang role:petugas|admin lama
 * sudah dihapus dari routes/api.php, RoutePermissionGate global yang
 * menggantikannya.
 */
class BedRoutePermissionGateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_role_kustom_tanpa_permission_ditolak_403(): void
    {
        $role = Role::create(['name' => 'kasir', 'guard_name' => config('auth.defaults.guard')]);
        $user = User::factory()->create();
        $user->assignRole($role);
        $this->actingAs($user, 'sanctum');

        $room = Room::factory()->create();

        $this->postJson('/api/v1/beds', ['room_id' => $room->id, 'bed_number' => 'B-01'])
            ->assertStatus(403);
    }

    public function test_role_kustom_dengan_permission_lolos_gerbang(): void
    {
        $role = Role::create(['name' => 'kasir', 'guard_name' => config('auth.defaults.guard')]);
        $role->givePermissionTo('general-bed.bed.store');
        $user = User::factory()->create();
        $user->assignRole($role);
        $this->actingAs($user, 'sanctum');

        $room = Room::factory()->create();

        $this->postJson('/api/v1/beds', ['room_id' => $room->id, 'bed_number' => 'B-01'])
            ->assertCreated();
    }

    public function test_index_tetap_terbuka_untuk_role_kustom_tanpa_permission_apa_pun_tapi_store_tetap_ditolak(): void
    {
        // beds.index bertier authenticated_any (dulu cuma auth:sanctum, tanpa
        // role: sama sekali) -- tidak digerbang permission sama sekali, lihat
        // RoutePermissionFixture::map(). store tetap butuh permission eksplisit.
        $role = Role::create(['name' => 'viewer', 'guard_name' => config('auth.defaults.guard')]);
        $user = User::factory()->create();
        $user->assignRole($role);
        $this->actingAs($user, 'sanctum');

        $bed = Bed::factory()->create();

        $this->getJson('/api/v1/beds')->assertOk();
        $this->postJson('/api/v1/beds', ['room_id' => $bed->room_id, 'bed_number' => 'B-99'])
            ->assertStatus(403);
    }
}
