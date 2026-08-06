<?php

namespace Modules\InventoryLinenTracking\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralWard\Models\Ward;
use Modules\InventoryLinenTracking\Models\LinenItem;
use Tests\TestCase;

class LinenItemControllerTest extends TestCase
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

    public function test_it_creates_a_linen_item(): void
    {
        $this->actingUser();
        $ward = Ward::factory()->create();

        $this->postJson('/api/v1/linen-items', [
            'linen_code' => 'LNN-00001',
            'linen_type' => 'sprei',
            'ward_id' => $ward->id,
        ])
            ->assertCreated()
            ->assertJsonPath('data.linen_code', 'LNN-00001')
            ->assertJsonPath('data.linen_type', 'sprei');
    }

    public function test_it_rejects_duplicate_linen_code(): void
    {
        $this->actingUser();
        LinenItem::factory()->create(['linen_code' => 'LNN-DUP']);

        $this->postJson('/api/v1/linen-items', [
            'linen_code' => 'LNN-DUP',
            'linen_type' => 'selimut',
        ])->assertStatus(422);
    }

    public function test_it_lists_linen_items(): void
    {
        $this->actingUser();
        LinenItem::factory()->count(3)->create();

        $this->getJson('/api/v1/linen-items')
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }
}
