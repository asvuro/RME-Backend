<?php

namespace Modules\GeneralEthnicity\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralEthnicity\Models\Ethnicity;
use Tests\TestCase;

class EthnicityControllerTest extends TestCase
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

    public function test_it_lists_ethnicities(): void
    {
        $this->actingUser();
        Ethnicity::factory()->count(2)->create();

        $this->getJson('/api/v1/ethnicities')->assertOk()->assertJsonCount(2, 'data');
    }

    public function test_it_creates_ethnicity(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/ethnicities', ['name' => 'Sample'])
            ->assertCreated()
            ->assertJsonPath('name', 'Sample');
    }

    public function test_it_deletes_ethnicity(): void
    {
        $this->actingUser();
        $item = Ethnicity::factory()->create();

        $this->deleteJson("/api/v1/ethnicities/{$item->id}")->assertStatus(204);
    }
}
