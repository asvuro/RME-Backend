<?php

namespace Modules\GeneralMaritalStatus\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralMaritalStatus\Models\MaritalStatus;
use Tests\TestCase;

class MaritalStatusControllerTest extends TestCase
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

    public function test_it_lists_marital_statuses(): void
    {
        $this->actingUser();
        MaritalStatus::factory()->count(2)->create();

        $this->getJson('/api/v1/marital_statuses')->assertOk()->assertJsonCount(2, 'data');
    }

    public function test_it_creates_maritalstatus(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/marital_statuses', ['name' => 'Sample'])
            ->assertCreated()
            ->assertJsonPath('name', 'Sample');
    }

    public function test_it_deletes_maritalstatus(): void
    {
        $this->actingUser();
        $item = MaritalStatus::factory()->create();

        $this->deleteJson("/api/v1/marital_statuses/{$item->id}")->assertStatus(204);
    }
}
