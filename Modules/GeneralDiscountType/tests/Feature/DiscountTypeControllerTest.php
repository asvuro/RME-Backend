<?php

namespace Modules\GeneralDiscountType\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralDiscountType\Models\DiscountType;
use Tests\TestCase;

class DiscountTypeControllerTest extends TestCase
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

    public function test_it_lists_discount_type(): void
    {
        $this->actingUser();
        DiscountType::factory()->count(3)->create();

        $this->getJson('/api/v1/discount-types')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_discount_type(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/discount-types', ['name' => 'Contoh Jenisdiskon', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Jenisdiskon');

        $this->assertDatabaseHas('discount_types', ['name' => 'Contoh Jenisdiskon']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        DiscountType::factory()->create(['name' => 'Contoh Jenisdiskon']);

        $this->postJson('/api/v1/discount-types', ['name' => 'Contoh Jenisdiskon'])->assertStatus(422);
    }

    public function test_it_deletes_discount_type(): void
    {
        $this->actingUser();
        $discountType = DiscountType::factory()->create();

        $this->deleteJson("/api/v1/discount-types/{$discountType->id}")->assertStatus(204);
        $this->assertDatabaseMissing('discount_types', ['id' => $discountType->id]);
    }
}