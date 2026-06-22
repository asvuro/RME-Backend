<?php

namespace Modules\GeneralMixtureInstruction\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralMixtureInstruction\Models\MixtureInstruction;
use Tests\TestCase;

class MixtureInstructionControllerTest extends TestCase
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

    public function test_it_lists_mixture_instruction(): void
    {
        $this->actingUser();
        MixtureInstruction::factory()->count(3)->create();

        $this->getJson('/api/v1/mixture-instructions')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_mixture_instruction(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/mixture-instructions', ['name' => 'Contoh Petunjukracikan', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Petunjukracikan');

        $this->assertDatabaseHas('mixture_instructions', ['name' => 'Contoh Petunjukracikan']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        MixtureInstruction::factory()->create(['name' => 'Contoh Petunjukracikan']);

        $this->postJson('/api/v1/mixture-instructions', ['name' => 'Contoh Petunjukracikan'])->assertStatus(422);
    }

    public function test_it_deletes_mixture_instruction(): void
    {
        $this->actingUser();
        $mixtureInstruction = MixtureInstruction::factory()->create();

        $this->deleteJson("/api/v1/mixture-instructions/{$mixtureInstruction->id}")->assertStatus(204);
        $this->assertDatabaseMissing('mixture_instructions', ['id' => $mixtureInstruction->id]);
    }
}