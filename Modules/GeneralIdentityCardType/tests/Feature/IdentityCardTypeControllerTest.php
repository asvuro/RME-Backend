<?php

namespace Modules\GeneralIdentityCardType\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralIdentityCardType\Models\IdentityCardType;
use Tests\TestCase;

class IdentityCardTypeControllerTest extends TestCase
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

    public function test_it_lists_identity_card_type(): void
    {
        $this->actingUser();
        IdentityCardType::factory()->count(3)->create();

        $this->getJson('/api/v1/identity-card-types')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_identity_card_type(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/identity-card-types', ['name' => 'Contoh Jeniskartuidentitas', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Jeniskartuidentitas');

        $this->assertDatabaseHas('identity_card_types', ['name' => 'Contoh Jeniskartuidentitas']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        IdentityCardType::factory()->create(['name' => 'Contoh Jeniskartuidentitas']);

        $this->postJson('/api/v1/identity-card-types', ['name' => 'Contoh Jeniskartuidentitas'])->assertStatus(422);
    }

    public function test_it_deletes_identity_card_type(): void
    {
        $this->actingUser();
        $identityCardType = IdentityCardType::factory()->create();

        $this->deleteJson("/api/v1/identity-card-types/{$identityCardType->id}")->assertStatus(204);
        $this->assertDatabaseMissing('identity_card_types', ['id' => $identityCardType->id]);
    }
}