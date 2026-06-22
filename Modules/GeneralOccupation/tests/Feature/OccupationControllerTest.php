<?php

namespace Modules\GeneralOccupation\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralOccupation\Models\Occupation;
use Tests\TestCase;

class OccupationControllerTest extends TestCase
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

    public function test_it_lists_occupations(): void
    {
        $this->actingUser();
        Occupation::factory()->count(2)->create();

        $this->getJson('/api/v1/occupations')->assertOk()->assertJsonCount(2, 'data');
    }

    public function test_it_creates_occupation(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/occupations', ['name' => 'Sample'])
            ->assertCreated()
            ->assertJsonPath('name', 'Sample');
    }

    public function test_it_deletes_occupation(): void
    {
        $this->actingUser();
        $item = Occupation::factory()->create();

        $this->deleteJson("/api/v1/occupations/{$item->id}")->assertStatus(204);
    }
}
