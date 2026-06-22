<?php

namespace Modules\GeneralMixtureType\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralMixtureType\Models\MixtureType;
use Tests\TestCase;

class MixtureTypeControllerTest extends TestCase
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

    public function test_it_lists_mixture_type(): void
    {
        $this->actingUser();
        MixtureType::factory()->count(3)->create();

        $this->getJson('/api/v1/mixture-types')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_mixture_type(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/mixture-types', ['name' => 'Contoh Racikan', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Racikan');

        $this->assertDatabaseHas('mixture_types', ['name' => 'Contoh Racikan']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        MixtureType::factory()->create(['name' => 'Contoh Racikan']);

        $this->postJson('/api/v1/mixture-types', ['name' => 'Contoh Racikan'])->assertStatus(422);
    }

    public function test_it_deletes_mixture_type(): void
    {
        $this->actingUser();
        $mixtureType = MixtureType::factory()->create();

        $this->deleteJson("/api/v1/mixture-types/{$mixtureType->id}")->assertStatus(204);
        $this->assertDatabaseMissing('mixture_types', ['id' => $mixtureType->id]);
    }
}