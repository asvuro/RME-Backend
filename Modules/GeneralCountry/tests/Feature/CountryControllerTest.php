<?php

namespace Modules\GeneralCountry\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralCountry\Models\Country;
use Tests\TestCase;

class CountryControllerTest extends TestCase
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

    public function test_it_lists_countries(): void
    {
        $this->actingUser();
        Country::factory()->count(2)->create();

        $this->getJson('/api/v1/countries')->assertOk()->assertJsonCount(2, 'data');
    }

    public function test_it_creates_country(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/countries', ['name' => 'Sample'])
            ->assertCreated()
            ->assertJsonPath('name', 'Sample');
    }

    public function test_it_deletes_country(): void
    {
        $this->actingUser();
        $item = Country::factory()->create();

        $this->deleteJson("/api/v1/countries/{$item->id}")->assertStatus(204);
    }
}
