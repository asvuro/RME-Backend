<?php

namespace Modules\GeneralReferralRoom\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralReferralRoom\Models\ReferralRoom;
use Tests\TestCase;

class ReferralRoomControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_referral_room(): void
    {
        $this->actingUser();
        ReferralRoom::factory()->count(3)->create();

        $this->getJson('/api/v1/referral-rooms')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_referral_room(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/referral-rooms', ['name' => 'Contoh Ruanganrujukan', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Ruanganrujukan');

        $this->assertDatabaseHas('referral_rooms', ['name' => 'Contoh Ruanganrujukan']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        ReferralRoom::factory()->create(['name' => 'Contoh Ruanganrujukan']);

        $this->postJson('/api/v1/referral-rooms', ['name' => 'Contoh Ruanganrujukan'])->assertStatus(422);
    }

    public function test_it_deletes_referral_room(): void
    {
        $this->actingUser();
        $referralRoom = ReferralRoom::factory()->create();

        $this->deleteJson("/api/v1/referral-rooms/{$referralRoom->id}")->assertStatus(204);
        $this->assertDatabaseMissing('referral_rooms', ['id' => $referralRoom->id]);
    }
}