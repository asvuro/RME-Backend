<?php

namespace Modules\GeneralLaboratoryUnit\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralLaboratoryUnit\Models\LaboratoryUnit;
use Tests\TestCase;

class LaboratoryUnitControllerTest extends TestCase
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

    public function test_it_lists_laboratory_unit(): void
    {
        $this->actingUser();
        LaboratoryUnit::factory()->count(3)->create();

        $this->getJson('/api/v1/laboratory-units')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_laboratory_unit(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/laboratory-units', ['name' => 'Contoh Satuanlaboratorium', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Satuanlaboratorium');

        $this->assertDatabaseHas('laboratory_units', ['name' => 'Contoh Satuanlaboratorium']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        LaboratoryUnit::factory()->create(['name' => 'Contoh Satuanlaboratorium']);

        $this->postJson('/api/v1/laboratory-units', ['name' => 'Contoh Satuanlaboratorium'])->assertStatus(422);
    }

    public function test_it_deletes_laboratory_unit(): void
    {
        $this->actingUser();
        $laboratoryUnit = LaboratoryUnit::factory()->create();

        $this->deleteJson("/api/v1/laboratory-units/{$laboratoryUnit->id}")->assertStatus(204);
        $this->assertDatabaseMissing('laboratory_units', ['id' => $laboratoryUnit->id]);
    }
}