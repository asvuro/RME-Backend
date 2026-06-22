<?php

namespace Modules\GeneralGender\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralGender\Models\Gender;
use Tests\TestCase;

class GenderControllerTest extends TestCase
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

    public function test_it_lists_genders(): void
    {
        $this->actingUser();
        Gender::factory()->count(2)->create();

        $this->getJson('/api/v1/genders')->assertOk()->assertJsonCount(2, 'data');
    }

    public function test_it_creates_gender(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/genders', ['name' => 'Male', 'code' => 'M'])
            ->assertCreated()
            ->assertJsonPath('name', 'Male');
    }

    public function test_it_deletes_gender(): void
    {
        $this->actingUser();
        $gender = Gender::factory()->create();

        $this->deleteJson("/api/v1/genders/{$gender->id}")->assertStatus(204);
    }
}
