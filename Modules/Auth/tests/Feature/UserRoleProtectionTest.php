<?php

namespace Modules\Auth\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Tests\TestCase;
use Database\Seeders\RoleAndPermissionSeeder;

class UserRoleProtectionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }

    private function actingUserWithRoles(array $roles): User
    {
        $user = User::factory()->create();
        $user->assignRole($roles);
        $this->actingAs($user, 'sanctum');

        return $user;
    }

    public function test_user_without_role_cannot_list_users(): void
    {
        $this->actingUserWithRoles([]);

        $this->getJson('/api/v1/users')->assertForbidden();
    }

    public function test_user_without_role_cannot_create_user(): void
    {
        $this->actingUserWithRoles([]);

        $response = $this->postJson('/api/v1/users', [
            'name' => 'Siti',
            'username' => 'siti',
            'email' => 'siti@example.com',
            'password' => 'Rahasia123',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('users', ['username' => 'siti']);
    }

    public function test_user_without_role_cannot_delete_user(): void
    {
        $this->actingUserWithRoles([]);
        $target = User::factory()->create();

        $this->deleteJson("/api/v1/users/{$target->id}")->assertForbidden();
        $this->assertDatabaseHas('users', ['id' => $target->id]);
    }

    public function test_admin_can_list_users(): void
    {
        $admin = $this->actingUserWithRoles(['admin']);
        User::factory()->count(3)->create();

        $this->getJson('/api/v1/users')
            ->assertOk()
            ->assertJsonCount(4, 'data');
    }

    public function test_admin_can_create_update_and_delete_users(): void
    {
        $this->actingUserWithRoles(['admin']);

        $created = $this->postJson('/api/v1/users', [
            'name' => 'Siti',
            'username' => 'siti',
            'email' => 'siti@example.com',
            'password' => 'Rahasia123',
        ]);
        $created->assertCreated();

        $target = User::where('username', 'siti')->firstOrFail();

        $this->putJson("/api/v1/users/{$target->id}", ['name' => 'Siti Baru'])
            ->assertOk()
            ->assertJsonPath('data.name', 'Siti Baru');

        $this->deleteJson("/api/v1/users/{$target->id}")->assertNoContent();
        $this->assertDatabaseMissing('users', ['id' => $target->id]);
    }

    public function test_petugas_role_still_forbidden_on_users_crud(): void
    {
        $this->actingUserWithRoles(['petugas']);
        User::factory()->create();

        $this->getJson('/api/v1/users')->assertForbidden();
        $this->postJson('/api/v1/users', [
            'name' => 'Siti',
            'username' => 'siti',
            'email' => 'siti@example.com',
            'password' => 'Rahasia123',
        ])->assertForbidden();
    }

    public function test_petugas_can_access_own_profile(): void
    {
        $petugas = $this->actingUserWithRoles(['petugas']);

        $this->getJson('/api/v1/me')
            ->assertOk()
            ->assertJsonPath('data.username', $petugas->username);
    }

    public function test_petugas_can_logout_own_session(): void
    {
        User::factory()->create(['username' => 'sari'])->assignRole('petugas');

        $login = $this->postJson('/api/v1/login', ['login' => 'sari', 'password' => 'password']);
        $login->assertOk();

        $token = $login->json('token');

        $this->withHeader('Authorization', "Bearer {$token}")->postJson('/api/v1/logout')->assertOk();
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
