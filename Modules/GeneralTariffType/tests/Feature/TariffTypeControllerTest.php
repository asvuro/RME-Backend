<?php

namespace Modules\GeneralTariffType\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralTariffType\Models\TariffType;
use Tests\TestCase;

class TariffTypeControllerTest extends TestCase
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

    public function test_it_lists_tariff_type(): void
    {
        $this->actingUser();
        TariffType::factory()->count(3)->create();

        $this->getJson('/api/v1/tariff-types')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_tariff_type(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/tariff-types', ['name' => 'Contoh Jenistarif', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Jenistarif');

        $this->assertDatabaseHas('tariff_types', ['name' => 'Contoh Jenistarif']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        TariffType::factory()->create(['name' => 'Contoh Jenistarif']);

        $this->postJson('/api/v1/tariff-types', ['name' => 'Contoh Jenistarif'])->assertStatus(422);
    }

    public function test_it_deletes_tariff_type(): void
    {
        $this->actingUser();
        $tariffType = TariffType::factory()->create();

        $this->deleteJson("/api/v1/tariff-types/{$tariffType->id}")->assertStatus(204);
        $this->assertDatabaseMissing('tariff_types', ['id' => $tariffType->id]);
    }
}