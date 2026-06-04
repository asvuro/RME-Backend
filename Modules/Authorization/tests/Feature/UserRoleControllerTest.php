<?php

namespace Modules\Authorization\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserRoleControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }

    private function actingAdmin(): User
    {
        $actor = User::factory()->create();
        $actor->assignRole('admin');
        $this->actingAs($actor, 'sanctum');

        return $actor;
    }

    public function test_it_assigns_roles_to_user(): void
    {
        $this->actingAdmin();

        $target = User::factory()->create();
        Role::create(['name' => 'staff']);

        $response = $this->putJson("/api/v1/users/{$target->id}/roles", [
            'roles' => ['staff'],
        ]);

        $response->assertOk()->assertJsonPath('roles.0', 'staff');
        $this->assertTrue($target->fresh()->hasRole('staff'));
    }

    public function test_it_rejects_sync_with_unknown_role(): void
    {
        $this->actingAdmin();

        $target = User::factory()->create();

        $response = $this->putJson("/api/v1/users/{$target->id}/roles", [
            'roles' => ['tidak-ada'],
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors('roles.0');
    }

    public function test_it_lists_user_roles(): void
    {
        $actor = $this->actingAdmin();

        Role::create(['name' => 'staff']);
        $actor->assignRole('staff');

        $response = $this->getJson("/api/v1/users/{$actor->id}/roles");

        $response->assertOk()->assertJsonCount(2, 'roles');
        $this->assertEqualsCanonicalizing(['admin', 'staff'], $response->json('roles'));
    }
}
