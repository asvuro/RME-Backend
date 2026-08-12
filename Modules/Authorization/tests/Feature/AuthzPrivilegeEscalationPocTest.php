<?php

namespace Modules\Authorization\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Modules\Auth\Models\User;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Security validation PoC (authorized white-box pentest).
 *
 * Verifies whether role/permission/user-role management endpoints are
 * reachable by an authenticated petugas (non-admin). AUTH_MATRIX.md claims
 * every write endpoint requires role:admin.
 */
class AuthzPrivilegeEscalationPocTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }

    private function actingPetugas(): User
    {
        $actor = User::factory()->create();
        $actor->assignRole('petugas');
        $this->actingAs($actor, 'sanctum');

        return $actor;
    }

    private function assertRouteIsAdminGated(string $method, string $uri): void
    {
        $route = Route::getRoutes()->getByName($this->routeNameFor($method, $uri))
            ?? Route::getRoutes()->match(request()->create($uri, $method));

        $middleware = $route->gatherMiddleware();

        $hasAdminGate = collect($middleware)->contains(
            fn ($m) => is_string($m) && str_starts_with($m, 'role:') && str_contains($m, 'admin')
        );

        $this->assertTrue(
            $hasAdminGate,
            "Expected {$method} {$uri} to be gated by role:admin, got middleware: ".implode(', ', $middleware)
        );
    }

    private function routeNameFor(string $method, string $uri): ?string
    {
        foreach (Route::getRoutes() as $route) {
            if (in_array(strtoupper($method), $route->methods(), true)
                && str_starts_with($uri.'/', rtrim($route->uri(), '/').'/')) {
                return $route->getName();
            }
        }

        return null;
    }

    public function test_petugas_cannot_create_role(): void
    {
        $this->actingPetugas();

        $response = $this->postJson('/api/v1/roles', [
            'name' => 'super-admin',
            'permissions' => [],
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('roles', ['name' => 'super-admin']);
    }

    public function test_petugas_cannot_sync_own_roles_to_admin(): void
    {
        $petugas = $this->actingPetugas();

        $response = $this->putJson("/api/v1/users/{$petugas->id}/roles", [
            'roles' => ['admin'],
        ]);

        $response->assertForbidden();
        $this->assertFalse($petugas->fresh()->hasRole('admin'));
    }

    public function test_petugas_cannot_escalate_other_user_to_admin(): void
    {
        $this->actingPetugas();
        $victim = User::factory()->create();

        $response = $this->putJson("/api/v1/users/{$victim->id}/roles", [
            'roles' => ['admin'],
        ]);

        $response->assertForbidden();
        $this->assertFalse($victim->fresh()->hasRole('admin'));
    }
}