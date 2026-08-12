<?php

namespace Modules\BerkasKlaimClaimFile\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\BerkasKlaimClaimFile\Models\ClaimFile;
use Modules\PendaftaranVisit\Models\Visit;
use Tests\TestCase;

/**
 * POC: claim-file creation accepts terminal statuses, skipping the
 * forward-only transition machine enforced on update().
 */
class ClaimFileStoreStateGapPocTest extends TestCase
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

    public function test_store_forces_initial_draft_state(): void
    {
        $visit = Visit::factory()->create();

        $response = $this->postJson('/api/v1/claim-files', [
            'visit_id' => $visit->id,
            'status' => 'paid',
        ]);

        if ($response->status() === 201 && $response->json('status') === 'paid') {
            fwrite(STDERR, "[POC-B] claim minted directly in terminal status 'paid'\n");
            $this->fail('[POC-B] store() accepted terminal status paid, bypassing transition machine');
        }

        $response->assertCreated();
        $this->assertSame('draft', $response->json('status'));
        $this->assertDatabaseHas('claim_files', ['visit_id' => $visit->id, 'status' => ClaimFile::STATUS_DRAFT]);
        $this->assertNull($response->json('submitted_at'));
    }

    public function test_created_claim_still_transitions_forward_normally(): void
    {
        $visit = Visit::factory()->create();

        $created = $this->postJson('/api/v1/claim-files', ['visit_id' => $visit->id])
            ->assertCreated();

        $claimId = $created->json('id');

        $this->putJson("/api/v1/claim-files/{$claimId}", ['status' => 'submitted'])
            ->assertOk()
            ->assertJsonPath('status', 'submitted');
    }
}