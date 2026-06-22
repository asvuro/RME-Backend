<?php

namespace Modules\GeneralDischargeCondition\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralDischargeCondition\Models\DischargeCondition;
use Tests\TestCase;

class DischargeConditionControllerTest extends TestCase
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

    public function test_it_lists_discharge_condition(): void
    {
        $this->actingUser();
        DischargeCondition::factory()->count(3)->create();

        $this->getJson('/api/v1/discharge-conditions')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_discharge_condition(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/discharge-conditions', ['name' => 'Contoh Keadaankeluar', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Keadaankeluar');

        $this->assertDatabaseHas('discharge_conditions', ['name' => 'Contoh Keadaankeluar']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        DischargeCondition::factory()->create(['name' => 'Contoh Keadaankeluar']);

        $this->postJson('/api/v1/discharge-conditions', ['name' => 'Contoh Keadaankeluar'])->assertStatus(422);
    }

    public function test_it_deletes_discharge_condition(): void
    {
        $this->actingUser();
        $dischargeCondition = DischargeCondition::factory()->create();

        $this->deleteJson("/api/v1/discharge-conditions/{$dischargeCondition->id}")->assertStatus(204);
        $this->assertDatabaseMissing('discharge_conditions', ['id' => $dischargeCondition->id]);
    }
}