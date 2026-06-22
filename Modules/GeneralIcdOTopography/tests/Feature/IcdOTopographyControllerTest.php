<?php

namespace Modules\GeneralIcdOTopography\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralIcdOTopography\Models\IcdOTopography;
use Tests\TestCase;

class IcdOTopographyControllerTest extends TestCase
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

    public function test_it_lists_icd_o_topographies(): void
    {
        $this->actingUser();
        IcdOTopography::factory()->count(3)->create();

        $this->getJson('/api/v1/icd-o-topographies')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_icd_o_topography(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/icd-o-topographies', ['name' => 'Paru-paru', 'code' => 'C34.9'])
            ->assertCreated()
            ->assertJsonPath('name', 'Paru-paru');

        $this->assertDatabaseHas('icd_o_topographies', ['name' => 'Paru-paru']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        IcdOTopography::factory()->create(['name' => 'Paru-paru']);

        $this->postJson('/api/v1/icd-o-topographies', ['name' => 'Paru-paru'])->assertStatus(422);
    }

    public function test_it_deletes_icd_o_topography(): void
    {
        $this->actingUser();
        $topography = IcdOTopography::factory()->create();

        $this->deleteJson("/api/v1/icd-o-topographies/{$topography->id}")->assertStatus(204);
        $this->assertDatabaseMissing('icd_o_topographies', ['id' => $topography->id]);
    }
}
