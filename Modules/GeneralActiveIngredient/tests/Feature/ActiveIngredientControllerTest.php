<?php

namespace Modules\GeneralActiveIngredient\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralActiveIngredient\Models\ActiveIngredient;
use Tests\TestCase;

class ActiveIngredientControllerTest extends TestCase
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

    public function test_it_lists_active_ingredient(): void
    {
        $this->actingUser();
        ActiveIngredient::factory()->count(3)->create();

        $this->getJson('/api/v1/active-ingredients')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_active_ingredient(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/active-ingredients', ['name' => 'Contoh Generikzataktif', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Generikzataktif');

        $this->assertDatabaseHas('active_ingredients', ['name' => 'Contoh Generikzataktif']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        ActiveIngredient::factory()->create(['name' => 'Contoh Generikzataktif']);

        $this->postJson('/api/v1/active-ingredients', ['name' => 'Contoh Generikzataktif'])->assertStatus(422);
    }

    public function test_it_deletes_active_ingredient(): void
    {
        $this->actingUser();
        $activeIngredient = ActiveIngredient::factory()->create();

        $this->deleteJson("/api/v1/active-ingredients/{$activeIngredient->id}")->assertStatus(204);
        $this->assertDatabaseMissing('active_ingredients', ['id' => $activeIngredient->id]);
    }
}