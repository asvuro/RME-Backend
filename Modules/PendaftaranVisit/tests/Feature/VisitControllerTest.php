<?php

namespace Modules\PendaftaranVisit\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralBed\Models\Bed;
use Modules\PembayaranInvoice\Services\InvoiceService;
use Modules\PendaftaranRegistration\Models\Registration;
use Modules\PendaftaranVisit\Models\Visit;
use Tests\TestCase;

class VisitControllerTest extends TestCase
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

    public function test_it_admits_a_visit_under_a_registration(): void
    {
        $this->actingUser();
        $registration = Registration::factory()->create();

        $response = $this->postJson('/api/v1/visits', ['registration_id' => $registration->id]);

        $response->assertCreated();
        $this->assertStringStartsWith('KJ-'.now()->format('Y').'-', $response->json('data.visit_number'));
    }

    public function test_it_lists_visits_filtered_by_registration(): void
    {
        $this->actingUser();
        $registration = Registration::factory()->create();
        Visit::factory()->count(2)->create(['registration_id' => $registration->id]);
        Visit::factory()->create();

        $response = $this->getJson("/api/v1/visits?registration_id={$registration->id}");

        $response->assertOk()->assertJsonCount(2, 'data');
    }

    public function test_it_blocks_discharge_via_update_and_points_to_the_gate(): void
    {
        $this->actingUser();
        $visit = Visit::factory()->create();

        // Gerbang pulang #11: edit bebas tidak boleh memutir discharged_at —
        // bed wajib dibebaskan lewat POST /visits/{visit}/discharge.
        $response = $this->putJson("/api/v1/visits/{$visit->id}", [
            'discharged_at' => now()->toIso8601String(),
            'final_outcome' => 'sembuh',
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseHas('visits', ['id' => $visit->id, 'discharged_at' => null]);
    }

    public function test_it_cancels_a_visit_and_releases_the_bed(): void
    {
        $this->actingUser();
        $bed = Bed::factory()->create(['status' => Bed::STATUS_OCCUPIED]);
        $visit = Visit::factory()->create(['ward_id' => $bed->room->ward_id, 'bed_id' => $bed->id]);

        $response = $this->deleteJson("/api/v1/visits/{$visit->id}");

        $response->assertStatus(204);
        $this->assertDatabaseHas('visits', ['id' => $visit->id, 'status' => 'cancelled']);
        $this->assertSame(Bed::STATUS_AVAILABLE, $bed->fresh()->status);
    }

    public function test_it_blocks_cancel_when_billing_is_locked(): void
    {
        $this->actingUser();
        $visit = Visit::factory()->create();
        $invoice = app(InvoiceService::class)->ensureForVisit($visit->id);
        app(InvoiceService::class)->lock($invoice->id);

        $response = $this->deleteJson("/api/v1/visits/{$visit->id}");

        $response->assertStatus(422);
        $this->assertDatabaseHas('visits', ['id' => $visit->id, 'status' => 'active']);
    }
}
