<?php

namespace Modules\SystemTteDocument\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralEmployee\Models\Employee;
use Modules\SystemTteDocument\Models\TteDocument;
use Tests\TestCase;

/**
 * State machine TTE internal: draft -> pending_sign -> signed -> locked.
 * sign() murni hitung SHA-256 dari `content` + tandai SIGNED -- tidak ada
 * panggilan eksternal PSrE/BSrE (future work).
 */
class TteDocumentControllerTest extends TestCase
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

    /** Petugas dengan profil Employee terhubung -- diperlukan untuk sign(). */
    private function actingUserWithEmployee(): Employee
    {
        $user = $this->actingUser();

        return Employee::factory()->create(['user_id' => $user->id]);
    }

    private function actingAdmin(): User
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $this->actingAs($user, 'sanctum');

        return $user;
    }

    public function test_it_creates_a_draft_document_and_lists_it(): void
    {
        $this->actingUser();

        // ref_type kini diizinkan hanya dari allowlist service dan ref_id-nya
        // wajib ada (temuan lanjutan vuln-0007: dokumen TTE tidak boleh
        // dicetak atas referensi fiktif).
        $store = $this->postJson('/api/v1/tte-documents', [
            'ref_type' => 'medical_record_resumes',
            'ref_id' => 1,
            'content' => ['title' => 'Resume Medis', 'body' => 'Isi resume.'],
        ]);

        $store->assertStatus(422);

        $visit = \Modules\PendaftaranVisit\Models\Visit::factory()->create();
        $store = $this->postJson('/api/v1/tte-documents', [
            'ref_type' => 'visits',
            'ref_id' => $visit->id,
            'content' => ['title' => 'Resume Medis', 'body' => 'Isi resume.'],
        ]);

        $store->assertCreated()
            ->assertJsonPath('data.status', TteDocument::STATUS_DRAFT)
            ->assertJsonPath('data.ref_type', 'visits')
            ->assertJsonPath('data.document_hash', null);

        $this->getJson('/api/v1/tte-documents')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_sign_computes_sha256_hash_and_advances_state_when_pending(): void
    {
        $employee = $this->actingUserWithEmployee();

        $content = ['title' => 'Resume Medis', 'body' => 'Isi resume final.'];
        $document = TteDocument::factory()->create([
            'status' => TteDocument::STATUS_PENDING_SIGN,
            'content' => $content,
        ]);

        $response = $this->postJson("/api/v1/tte-documents/{$document->id}/sign");

        $response->assertOk()
            ->assertJsonPath('data.status', TteDocument::STATUS_SIGNED)
            ->assertJsonPath('data.signed_by', $employee->id);

        $expectedHash = hash('sha256', json_encode($content));
        $this->assertSame($expectedHash, $response->json('data.document_hash'));

        $document->refresh();
        $this->assertSame(TteDocument::STATUS_SIGNED, $document->status);
        $this->assertNotNull($document->signed_at);
        $this->assertSame($expectedHash, $document->document_hash);
    }

    public function test_sign_is_rejected_when_document_is_still_draft(): void
    {
        $this->actingUserWithEmployee();

        $document = TteDocument::factory()->create(['status' => TteDocument::STATUS_DRAFT]);

        $this->postJson("/api/v1/tte-documents/{$document->id}/sign")->assertStatus(422);

        $document->refresh();
        $this->assertSame(TteDocument::STATUS_DRAFT, $document->status);
        $this->assertNull($document->document_hash);
        $this->assertNull($document->signed_by);
    }

    public function test_sign_is_rejected_when_document_already_signed(): void
    {
        $this->actingUserWithEmployee();

        $document = TteDocument::factory()->create([
            'status' => TteDocument::STATUS_SIGNED,
            'document_hash' => 'already-signed-hash',
            'signed_at' => now(),
        ]);

        $this->postJson("/api/v1/tte-documents/{$document->id}/sign")->assertStatus(422);
    }

    public function test_sign_is_rejected_when_caller_has_no_linked_employee_profile(): void
    {
        $this->actingUser();

        $document = TteDocument::factory()->create(['status' => TteDocument::STATUS_PENDING_SIGN]);

        $this->postJson("/api/v1/tte-documents/{$document->id}/sign")->assertStatus(422);

        $this->assertSame(TteDocument::STATUS_PENDING_SIGN, $document->fresh()->status);
    }

    public function test_sign_cannot_attribute_to_an_arbitrary_employee_id(): void
    {
        $this->actingUserWithEmployee();
        $victim = Employee::factory()->create();

        $document = TteDocument::factory()->create(['status' => TteDocument::STATUS_PENDING_SIGN]);

        $this->postJson("/api/v1/tte-documents/{$document->id}/sign", [
            'employee_id' => $victim->id,
        ])->assertOk();

        $this->assertNotSame($victim->id, $document->fresh()->signed_by);
    }

    public function test_create_is_rejected_when_an_active_document_already_exists_for_the_reference(): void
    {
        $this->actingUser();

        $visit = \Modules\PendaftaranVisit\Models\Visit::factory()->create();
        TteDocument::factory()->create([
            'ref_type' => 'visits',
            'ref_id' => $visit->id,
            'status' => TteDocument::STATUS_PENDING_SIGN,
        ]);

        $this->postJson('/api/v1/tte-documents', [
            'ref_type' => 'visits',
            'ref_id' => $visit->id,
        ])->assertStatus(422);
    }

    public function test_submit_for_sign_then_sign_then_lock_full_lifecycle(): void
    {
        $this->actingUserWithEmployee();

        $document = TteDocument::factory()->create(['status' => TteDocument::STATUS_DRAFT]);

        $this->postJson("/api/v1/tte-documents/{$document->id}/submit-for-sign")
            ->assertOk()
            ->assertJsonPath('data.status', TteDocument::STATUS_PENDING_SIGN);

        $this->postJson("/api/v1/tte-documents/{$document->id}/sign")
            ->assertOk()
            ->assertJsonPath('data.status', TteDocument::STATUS_SIGNED);

        // lock() dibatasi admin -- petugas penanda tangan tidak boleh mengunci sendiri.
        $this->postJson("/api/v1/tte-documents/{$document->id}/lock")->assertForbidden();

        $this->actingAdmin();
        $this->postJson("/api/v1/tte-documents/{$document->id}/lock")
            ->assertOk()
            ->assertJsonPath('data.status', TteDocument::STATUS_LOCKED);

        // Locked dokumen tak bisa ditandatangani ulang.
        $this->postJson("/api/v1/tte-documents/{$document->id}/sign")->assertStatus(422);
    }

    public function test_show_returns_a_single_document(): void
    {
        $this->actingUser();
        $document = TteDocument::factory()->create();

        $this->getJson("/api/v1/tte-documents/{$document->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $document->id);
    }

    public function test_endpoints_are_closed_to_guests(): void
    {
        $this->postJson('/api/v1/tte-documents', [
            'ref_type' => 'medical_record_resumes',
            'ref_id' => 1,
        ])->assertUnauthorized();
    }
}
