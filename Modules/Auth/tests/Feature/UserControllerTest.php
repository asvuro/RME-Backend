<?php

namespace Modules\Auth\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Tests\TestCase;
use Database\Seeders\RoleAndPermissionSeeder;

class UserControllerTest extends TestCase
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

    public function test_it_lists_users(): void
    {
        $this->actingUser();
        User::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/users');

        $response->assertOk()->assertJsonCount(4, 'data');
    }

    public function test_it_creates_user(): void
    {
        $this->actingUser();

        $response = $this->postJson('/api/v1/users', [
            'name' => 'Siti',
            'username' => 'siti',
            'email' => 'siti@example.com',
            'password' => 'Rahasia123',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('users', ['username' => 'siti']);
    }

    public function test_it_updates_user(): void
    {
        $this->actingUser();
        $target = User::factory()->create();

        $response = $this->putJson("/api/v1/users/{$target->id}", ['name' => 'Baru']);

        $response->assertOk()->assertJsonPath('data.name', 'Baru');
    }

    public function test_it_deletes_user(): void
    {
        $this->actingUser();
        $target = User::factory()->create();

        $response = $this->deleteJson("/api/v1/users/{$target->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('users', ['id' => $target->id]);
    }

    public function test_admin_cannot_lock_own_account(): void
    {
        $admin = $this->actingUser();

        $this->putJson("/api/v1/users/{$admin->id}", ['is_locked' => true])
            ->assertOk()
            ->assertJsonPath('data.is_locked', false);

        $this->assertFalse($admin->fresh()->is_locked);
    }

    public function test_admin_cannot_disable_own_account(): void
    {
        $admin = $this->actingUser();

        $this->putJson("/api/v1/users/{$admin->id}", ['is_active' => false])
            ->assertOk()
            ->assertJsonPath('data.is_active', true);

        $this->assertTrue($admin->fresh()->is_active);
    }

    public function test_admin_can_still_lock_other_accounts(): void
    {
        $this->actingUser();
        $target = User::factory()->create();

        $this->putJson("/api/v1/users/{$target->id}", ['is_locked' => true])
            ->assertOk()
            ->assertJsonPath('data.is_locked', true);
    }

    public function test_admin_cannot_delete_own_account(): void
    {
        $admin = $this->actingUser();

        $this->deleteJson("/api/v1/users/{$admin->id}")->assertStatus(422);

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }
}
