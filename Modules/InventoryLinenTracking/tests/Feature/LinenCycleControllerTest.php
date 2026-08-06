<?php

namespace Modules\InventoryLinenTracking\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralWard\Models\Ward;
use Modules\InventoryLinenTracking\Models\LinenCycle;
use Modules\InventoryLinenTracking\Models\LinenItem;
use Tests\TestCase;

class LinenCycleControllerTest extends TestCase
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

    public function test_it_creates_a_linen_cycle_starting_at_dikirim_londri(): void
    {
        $this->actingUser();
        $item = LinenItem::factory()->create();

        $this->postJson('/api/v1/linen-cycles', [
            'linen_item_id' => $item->id,
            'quantity' => 10,
        ])
            ->assertCreated()
            ->assertJsonPath('data.status', 'dikirim_londri')
            ->assertJsonPath('data.quantity', 10);
    }

    public function test_it_rejects_creating_a_cycle_with_non_initial_status(): void
    {
        $this->actingUser();
        $item = LinenItem::factory()->create();

        $this->postJson('/api/v1/linen-cycles', [
            'linen_item_id' => $item->id,
            'status' => 'kembali_bersih',
        ])->assertStatus(422);
    }

    /** Gerbang bisnis utama: transisi status wajib ikut urutan matriks di LinenCycleService. */
    public function test_it_rejects_skipping_a_status_transition(): void
    {
        $this->actingUser();
        $cycle = LinenCycle::factory()->create(['status' => LinenCycle::STATUS_DIKIRIM_LONDRI]);

        // Loncat langsung ke kembali_bersih tanpa lewat dicuci — harus ditolak.
        $this->putJson("/api/v1/linen-cycles/{$cycle->id}", [
            'status' => 'kembali_bersih',
        ])->assertStatus(422);

        $this->assertSame(LinenCycle::STATUS_DIKIRIM_LONDRI, $cycle->fresh()->status);
    }

    public function test_it_allows_valid_status_transition_and_stamps_received_at(): void
    {
        $this->actingUser();
        $cycle = LinenCycle::factory()->create(['status' => LinenCycle::STATUS_DIKIRIM_LONDRI]);

        $this->putJson("/api/v1/linen-cycles/{$cycle->id}", [
            'status' => 'dicuci',
        ])->assertOk()->assertJsonPath('data.status', 'dicuci');

        $this->putJson("/api/v1/linen-cycles/{$cycle->id}", [
            'status' => 'kembali_bersih',
        ])->assertOk()->assertJsonPath('data.status', 'kembali_bersih');

        $this->assertNotNull($cycle->fresh()->received_at);
    }

    public function test_it_filters_linen_cycles_by_status_and_ward_id(): void
    {
        $this->actingUser();
        $ward = Ward::factory()->create();
        $otherWard = Ward::factory()->create();

        $itemInWard = LinenItem::factory()->create(['ward_id' => $ward->id]);
        $itemInOtherWard = LinenItem::factory()->create(['ward_id' => $otherWard->id]);

        $matching = LinenCycle::factory()->create(['linen_item_id' => $itemInWard->id, 'status' => LinenCycle::STATUS_DIKIRIM_LONDRI]);
        LinenCycle::factory()->create(['linen_item_id' => $itemInWard->id, 'status' => LinenCycle::STATUS_DICUCI]);
        LinenCycle::factory()->create(['linen_item_id' => $itemInOtherWard->id, 'status' => LinenCycle::STATUS_DIKIRIM_LONDRI]);

        $response = $this->getJson("/api/v1/linen-cycles?status=dikirim_londri&ward_id={$ward->id}");

        $response->assertOk();
        $ids = array_column($response->json('data'), 'id');
        $this->assertSame([$matching->id], $ids);
    }
}
