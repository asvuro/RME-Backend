<?php

namespace Modules\GeneralReferralType\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralReferralType\Models\ReferralType;
use Tests\TestCase;

class ReferralTypeControllerTest extends TestCase
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

    public function test_it_lists_referral_type(): void
    {
        $this->actingUser();
        ReferralType::factory()->count(3)->create();

        $this->getJson('/api/v1/referral-types')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_referral_type(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/referral-types', ['name' => 'Contoh Jenisrujukan', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Jenisrujukan');

        $this->assertDatabaseHas('referral_types', ['name' => 'Contoh Jenisrujukan']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        ReferralType::factory()->create(['name' => 'Contoh Jenisrujukan']);

        $this->postJson('/api/v1/referral-types', ['name' => 'Contoh Jenisrujukan'])->assertStatus(422);
    }

    public function test_it_deletes_referral_type(): void
    {
        $this->actingUser();
        $referralType = ReferralType::factory()->create();

        $this->deleteJson("/api/v1/referral-types/{$referralType->id}")->assertStatus(204);
        $this->assertDatabaseMissing('referral_types', ['id' => $referralType->id]);
    }
}