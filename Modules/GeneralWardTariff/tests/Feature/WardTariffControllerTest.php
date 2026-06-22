<?php

namespace Modules\GeneralWardTariff\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralWardTariff\Models\WardTariff;
use Tests\TestCase;

class WardTariffControllerTest extends TestCase
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

    public function test_it_lists_items(): void
    {
        $this->actingUser();
        WardTariff::factory()->count(3)->create();

        $this->getJson('/api/v1/ward-tariffs')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_item(): void
    {
        $this->actingUser();
        $payload = WardTariff::factory()->make()->toArray();
        $this->postJson('/api/v1/ward-tariffs', $payload)->assertCreated();
    }

    public function test_it_updates_item(): void
    {
        $this->actingUser();
        $item = WardTariff::factory()->create();
        $payload = WardTariff::factory()->make()->toArray();
        $this->putJson("/api/v1/ward-tariffs/{$item->id}", $payload)->assertOk();
    }

    public function test_it_deletes_item(): void
    {
        $this->actingUser();
        $item = WardTariff::factory()->create();
        $this->deleteJson("/api/v1/ward-tariffs/{$item->id}")->assertStatus(204);
    }
}
