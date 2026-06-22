<?php

namespace Modules\GeneralDepositType\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralDepositType\Models\DepositType;
use Tests\TestCase;

class DepositTypeControllerTest extends TestCase
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

    public function test_it_lists_deposit_type(): void
    {
        $this->actingUser();
        DepositType::factory()->count(3)->create();

        $this->getJson('/api/v1/deposit-types')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_deposit_type(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/deposit-types', ['name' => 'Contoh Jenisdeposit', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Jenisdeposit');

        $this->assertDatabaseHas('deposit_types', ['name' => 'Contoh Jenisdeposit']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        DepositType::factory()->create(['name' => 'Contoh Jenisdeposit']);

        $this->postJson('/api/v1/deposit-types', ['name' => 'Contoh Jenisdeposit'])->assertStatus(422);
    }

    public function test_it_deletes_deposit_type(): void
    {
        $this->actingUser();
        $depositType = DepositType::factory()->create();

        $this->deleteJson("/api/v1/deposit-types/{$depositType->id}")->assertStatus(204);
        $this->assertDatabaseMissing('deposit_types', ['id' => $depositType->id]);
    }
}