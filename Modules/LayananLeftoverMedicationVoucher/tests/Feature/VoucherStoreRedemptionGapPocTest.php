<?php

namespace Modules\LayananLeftoverMedicationVoucher\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\LayananLeftoverMedicationVoucher\Models\LeftoverMedicationVoucher;
use Tests\TestCase;

/**
 * POC: voucher store path still accepts born-redeemed status plus an
 * attacker-chosen redeemed_at, defeating the update-path timestamp fix.
 */
class VoucherStoreRedemptionGapPocTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
        $user = User::factory()->create();
        $user->assignRole('petugas');
        $this->actingAs($user, 'sanctum');
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'voucher_number' => 'VCH-POC-'.uniqid(),
            'visit_id' => \Modules\PendaftaranVisit\Models\Visit::factory()->create()->id,
            'patient_id' => \Modules\GeneralPatient\Models\Patient::factory()->create()->id,
            'issued_at' => '2026-08-26 08:00:00',
        ], $overrides);
    }

    public function test_store_forces_pending_and_drops_client_redeemed_at(): void
    {
        $response = $this->postJson('/api/v1/leftover-medication-vouchers', $this->payload([
            'status' => 'redeemed',
            'redeemed_at' => '2020-01-01 00:00:00',
        ]));

        $fresh = LeftoverMedicationVoucher::query()
            ->where('voucher_number', $response->json('data.voucher_number'))
            ->first();

        if ($fresh !== null && ($fresh->status === 'redeemed' || $fresh->redeemed_at !== null)) {
            fwrite(STDERR, sprintf(
                "[POC-C] voucher born-redeemed status=%s redeemed_at=%s (client-chosen)\n",
                $fresh->status,
                $fresh->redeemed_at,
            ));
            $this->fail('[POC-C] store accepted born-redeemed voucher with forged redeemed_at');
        }

        $response->assertCreated();
        $this->assertNotNull($fresh);
        $this->assertSame('pending', $fresh->status);
        $this->assertNull($fresh->redeemed_at);
    }

    public function test_new_voucher_can_still_be_redeemed_through_update_path(): void
    {
        $created = $this->postJson('/api/v1/leftover-medication-vouchers', $this->payload())
            ->assertCreated();

        $voucherId = $created->json('data.id');

        $this->putJson("/api/v1/leftover-medication-vouchers/{$voucherId}", ['status' => 'redeemed'])
            ->assertOk()
            ->assertJsonPath('data.status', 'redeemed');

        $fresh = LeftoverMedicationVoucher::find($voucherId);
        $this->assertNotNull($fresh->redeemed_at);
        $this->assertTrue($fresh->redeemed_at->greaterThan(now()->subMinute()));
    }
}