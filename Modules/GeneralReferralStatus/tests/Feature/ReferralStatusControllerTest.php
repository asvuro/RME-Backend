<?php

namespace Modules\GeneralReferralStatus\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralReferralStatus\Models\ReferralStatus;
use Tests\TestCase;

class ReferralStatusControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_referral_statuse(): void
    {
        $this->actingUser();
        ReferralStatus::factory()->count(3)->create();

        $this->getJson('/api/v1/referral-statuses')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_referral_status(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/referral-statuses', ['name' => 'Contoh Statusrujukan', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Statusrujukan');

        $this->assertDatabaseHas('referral_statuses', ['name' => 'Contoh Statusrujukan']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        ReferralStatus::factory()->create(['name' => 'Contoh Statusrujukan']);

        $this->postJson('/api/v1/referral-statuses', ['name' => 'Contoh Statusrujukan'])->assertStatus(422);
    }

    public function test_it_deletes_referral_status(): void
    {
        $this->actingUser();
        $referralStatus = ReferralStatus::factory()->create();

        $this->deleteJson("/api/v1/referral-statuses/{$referralStatus->id}")->assertStatus(204);
        $this->assertDatabaseMissing('referral_statuses', ['id' => $referralStatus->id]);
    }
}