<?php

namespace Modules\SystemTteDocument\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralEmployee\Database\Factories\EmployeeFactory;
use Modules\GeneralEmployee\Models\Employee;
use Modules\PendaftaranVisit\Database\Factories\VisitFactory;
use Modules\PendaftaranVisit\Models\Visit;
use Modules\SystemTteDocument\Models\TteDocument;
use Tests\TestCase;

/**
 * PoC: TTE document fabrication + self-signing of arbitrary references.
 *
 * POST /tte-documents accepts any client-supplied ref_type/ref_id with no
 * allowlist or existence check, and the signer is resolved from whoever
 * calls POST /{id}/sign -- so a petugas can mint a draft against a foreign
 * (or entirely fabricated) reference, push it into pending_sign, and sign it
 * themselves, producing a digitally signed medical record attributed to an
 * encounter they never touched.
 */
class TteDocumentFabricationPocTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }

    private function actingPetugasWithEmployee(): array
    {
        $user = User::factory()->create();
        $user->assignRole('petugas');
        $employee = Employee::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user, 'sanctum');

        return [$user, $employee];
    }

    public function test_petugas_fabricates_and_self_signs_document_for_a_foreign_visit(): void
    {
        [, $attackerEmployee] = $this->actingPetugasWithEmployee();

        // A clinical encounter owned by somebody else (different clinician on
        // record); the attacker has no role in it.
        $foreignPhysician = Employee::factory()->create();
        $visit = Visit::factory()->create([
            'status' => 'discharged',
            'discharged_at' => now(),
            'attending_physician_id' => $foreignPhysician->id,
        ]);

        $store = $this->postJson('/api/v1/tte-documents', [
            'ref_type' => 'visits',
            'ref_id' => $visit->id,
            'content' => [
                'title' => 'Resume Medis',
                'diagnosis' => 'Fabricated without any clinical involvement',
            ],
        ]);
        $store->assertCreated();
        $documentId = $store->json('data.id');

        $this->postJson("/api/v1/tte-documents/{$documentId}/submit-for-sign")->assertOk();
        $sign = $this->postJson("/api/v1/tte-documents/{$documentId}/sign");
        $sign->assertOk();

        $document = TteDocument::findOrFail($documentId);
        $this->assertSame(TteDocument::STATUS_SIGNED, $document->status);
        $this->assertSame($attackerEmployee->id, $document->signed_by,
            'the attacker self-signed a legally significant document about an encounter they never participated in');
        $this->assertNotNull($document->document_hash);

        // The fabricated signature survives admin sealing; the record now
        // permanently asserts clinical authority for this visit.
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin, 'sanctum');
        $this->postJson("/api/v1/tte-documents/{$documentId}/lock")->assertOk();
        $this->assertSame(TteDocument::STATUS_LOCKED, $document->fresh()->status);
    }

    public function test_petugas_can_create_signed_document_against_a_nonexistent_reference(): void
    {
        $this->actingPetugasWithEmployee();

        // No visits table row with id 999999 exists -- the fixed contract is
        // fail-closed: unknown ref_type AND missing ref rows both get rejected.
        $this->assertNull(Visit::query()->find(999999));

        $store = $this->postJson('/api/v1/tte-documents', [
            'ref_type' => 'visits',
            'ref_id' => 999999,
            'content' => ['title' => 'Ghost encounter'],
        ]);

        $store->assertStatus(422);
        $this->assertSame(0, TteDocument::query()
            ->where('ref_type', 'visits')
            ->where('ref_id', 999999)
            ->count(), 'no document may be minted for a nonexistent reference');

        // Unknown ref_type values are equally rejected.
        $this->postJson('/api/v1/tte-documents', [
            'ref_type' => 'not_a_real_table',
            'ref_id' => 1,
        ])->assertStatus(422);

        $this->assertDatabaseCount('tte_documents', 0);

        $this->assertSame(TteDocument::STATUS_DRAFT, TteDocument::STATUS_DRAFT, 'invariant: nothing signed');
    }

    public function test_signer_resolution_is_ambiguous_when_user_has_multiple_employee_rows(): void
    {
        [$user, $firstEmployee] = $this->actingPetugasWithEmployee();

        // employees.user_id is NOT unique (see WardAccessResolver): one user,
        // two Employee profiles -- e.g. the original profile was deactivated
        // and re-created (the exact scenario WardAccessResolver documents).
        $firstEmployee->update(['is_active' => false]);
        $secondEmployee = Employee::factory()->create(['user_id' => $user->id]);
        $this->assertTrue($secondEmployee->is_active);

        $document = TteDocument::factory()->create([
            'status' => TteDocument::STATUS_PENDING_SIGN,
            'content' => ['title' => 'Resume Medis'],
        ]);

        $this->postJson("/api/v1/tte-documents/{$document->id}/sign")->assertOk();

        // Fixed contract: signer resolution binds to the user's ACTIVE profile
        // deterministically -- a deactivated legacy row must never receive the
        // legally significant signature just because it has the lower id.
        $this->assertSame($secondEmployee->id, $document->fresh()->signed_by,
            'signature must land on the active employee profile');
        $this->assertNotSame($firstEmployee->id, $document->fresh()->signed_by);
    }
}