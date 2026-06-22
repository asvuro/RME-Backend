<?php

namespace Modules\GeneralDosageInstruction\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralDosageInstruction\Models\DosageInstruction;
use Tests\TestCase;

class DosageInstructionControllerTest extends TestCase
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

    public function test_it_lists_dosage_instruction(): void
    {
        $this->actingUser();
        DosageInstruction::factory()->count(3)->create();

        $this->getJson('/api/v1/dosage-instructions')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_dosage_instruction(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/dosage-instructions', ['name' => 'Contoh Aturanpakai', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Aturanpakai');

        $this->assertDatabaseHas('dosage_instructions', ['name' => 'Contoh Aturanpakai']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        DosageInstruction::factory()->create(['name' => 'Contoh Aturanpakai']);

        $this->postJson('/api/v1/dosage-instructions', ['name' => 'Contoh Aturanpakai'])->assertStatus(422);
    }

    public function test_it_deletes_dosage_instruction(): void
    {
        $this->actingUser();
        $dosageInstruction = DosageInstruction::factory()->create();

        $this->deleteJson("/api/v1/dosage-instructions/{$dosageInstruction->id}")->assertStatus(204);
        $this->assertDatabaseMissing('dosage_instructions', ['id' => $dosageInstruction->id]);
    }
}