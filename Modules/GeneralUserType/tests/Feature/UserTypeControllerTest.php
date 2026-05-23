<?php

namespace Modules\GeneralUserType\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralUserType\Models\UserType;
use Tests\TestCase;

class UserTypeControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_user_type(): void
    {
        $this->actingUser();
        UserType::factory()->count(3)->create();

        $this->getJson('/api/v1/user-types')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_user_type(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/user-types', ['name' => 'Contoh Jenispengguna', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Jenispengguna');

        $this->assertDatabaseHas('user_types', ['name' => 'Contoh Jenispengguna']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        UserType::factory()->create(['name' => 'Contoh Jenispengguna']);

        $this->postJson('/api/v1/user-types', ['name' => 'Contoh Jenispengguna'])->assertStatus(422);
    }

    public function test_it_deletes_user_type(): void
    {
        $this->actingUser();
        $userType = UserType::factory()->create();

        $this->deleteJson("/api/v1/user-types/{$userType->id}")->assertStatus(204);
        $this->assertDatabaseMissing('user_types', ['id' => $userType->id]);
    }
}