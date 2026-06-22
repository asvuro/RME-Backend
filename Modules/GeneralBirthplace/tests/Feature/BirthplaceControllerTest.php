<?php

namespace Modules\GeneralBirthplace\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralBirthplace\Models\Birthplace;
use Tests\TestCase;

class BirthplaceControllerTest extends TestCase
{
    use RefreshDatabase;


    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }
    private function actingUser(): void
    {
        $user = User::factory()->create();
        $user->assignRole('petugas');
        $this->actingAs($user, 'sanctum');
    }

    public function test_it_lists_birthplaces(): void
    {
        $this->actingUser();
        Birthplace::factory()->count(3)->create();

        $this->getJson('/api/v1/birthplaces')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_birthplace(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/birthplaces', [
            'name' => 'Test Name',
        ])->assertCreated();

        $this->assertDatabaseCount('birthplaces', 1);
    }

    public function test_it_deletes_birthplace(): void
    {
        $this->actingUser();
        $birthplace = Birthplace::factory()->create();

        $this->deleteJson("/api/v1/birthplaces/{$birthplace->id}")->assertStatus(204);
        $this->assertDatabaseMissing('birthplaces', ['id' => $birthplace->id]);
    }

    public function test_it_shows_birthplace(): void
    {
        $this->actingUser();
        $birthplace = Birthplace::factory()->create();

        $this->getJson("/api/v1/birthplaces/{$birthplace->id}")->assertOk()->assertJsonPath('data.id', $birthplace->id);
    }

}
