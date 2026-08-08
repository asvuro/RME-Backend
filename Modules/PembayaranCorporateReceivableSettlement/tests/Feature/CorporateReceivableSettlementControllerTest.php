<?php

namespace Modules\PembayaranCorporateReceivableSettlement\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\PembayaranCorporateReceivable\Models\CorporateReceivable;
use Modules\PembayaranCorporateReceivableSettlement\Models\CorporateReceivableSettlement;
use Tests\TestCase;

class CorporateReceivableSettlementControllerTest extends TestCase
{
    use RefreshDatabase;


    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }
    private function actingUser(): User
    {
        $user = User::factory()->create();
        $user->assignRole('petugas');
        $this->actingAs($user, 'sanctum');

        return $user;
    }

    public function test_it_lists_settlements(): void
    {
        $this->actingUser();
        CorporateReceivableSettlement::factory()->count(3)->create();

        $this->getJson('/api/v1/corporate-receivable-settlements')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_settlement_and_marks_receivable_settled(): void
    {
        $user = $this->actingUser();
        $receivable = CorporateReceivable::factory()->create(['status' => 'outstanding', 'amount' => 2000000]);

        $this->postJson('/api/v1/corporate-receivable-settlements', [
            'corporate_receivable_id' => $receivable->id,
            'paid_amount' => 2000000,
        ])->assertCreated()->assertJsonPath('data.received_by', $user->id);

        $this->assertDatabaseHas('corporate_receivable_settlements', ['corporate_receivable_id' => $receivable->id, 'received_by' => $user->id]);
        $this->assertEquals('settled', $receivable->fresh()->status);
    }

    public function test_it_rejects_settlement_exceeding_receivable_balance(): void
    {
        $this->actingUser();
        $receivable = CorporateReceivable::factory()->create(['status' => 'outstanding', 'amount' => 500]);

        $this->postJson('/api/v1/corporate-receivable-settlements', [
            'corporate_receivable_id' => $receivable->id,
            'paid_amount' => 999999,
        ])->assertStatus(422);

        $this->assertDatabaseCount('corporate_receivable_settlements', 0);
        $this->assertEquals('outstanding', $receivable->fresh()->status);
    }

    public function test_it_rejects_settlement_replay_after_already_settled(): void
    {
        $this->actingUser();
        $receivable = CorporateReceivable::factory()->create(['status' => 'outstanding', 'amount' => 500]);

        $this->postJson('/api/v1/corporate-receivable-settlements', [
            'corporate_receivable_id' => $receivable->id,
            'paid_amount' => 500,
        ])->assertCreated();

        $this->postJson('/api/v1/corporate-receivable-settlements', [
            'corporate_receivable_id' => $receivable->id,
            'paid_amount' => 1,
        ])->assertStatus(422);

        $this->assertDatabaseCount('corporate_receivable_settlements', 1);
    }

    public function test_it_shows_settlement(): void
    {
        $this->actingUser();
        $settlement = CorporateReceivableSettlement::factory()->create();

        $this->getJson("/api/v1/corporate-receivable-settlements/{$settlement->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $settlement->id);
    }

    public function test_it_has_no_update_or_delete_routes(): void
    {
        $this->actingUser();
        $settlement = CorporateReceivableSettlement::factory()->create();

        $this->putJson("/api/v1/corporate-receivable-settlements/{$settlement->id}", ['paid_amount' => 1])->assertStatus(405);
        $this->deleteJson("/api/v1/corporate-receivable-settlements/{$settlement->id}")->assertStatus(405);
    }

    public function test_guest_cannot_access_settlements(): void
    {
        $this->getJson('/api/v1/corporate-receivable-settlements')->assertStatus(401);
    }
}
