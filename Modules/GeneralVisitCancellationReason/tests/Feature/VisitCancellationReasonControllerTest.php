<?php

namespace Modules\GeneralVisitCancellationReason\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralVisitCancellationReason\Models\VisitCancellationReason;
use Tests\TestCase;

class VisitCancellationReasonControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_visit_cancellation_reason(): void
    {
        $this->actingUser();
        VisitCancellationReason::factory()->count(3)->create();

        $this->getJson('/api/v1/visit-cancellation-reasons')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_visit_cancellation_reason(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/visit-cancellation-reasons', ['name' => 'Contoh Alasanpembatalankunjungan', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Alasanpembatalankunjungan');

        $this->assertDatabaseHas('visit_cancellation_reasons', ['name' => 'Contoh Alasanpembatalankunjungan']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        VisitCancellationReason::factory()->create(['name' => 'Contoh Alasanpembatalankunjungan']);

        $this->postJson('/api/v1/visit-cancellation-reasons', ['name' => 'Contoh Alasanpembatalankunjungan'])->assertStatus(422);
    }

    public function test_it_deletes_visit_cancellation_reason(): void
    {
        $this->actingUser();
        $visitCancellationReason = VisitCancellationReason::factory()->create();

        $this->deleteJson("/api/v1/visit-cancellation-reasons/{$visitCancellationReason->id}")->assertStatus(204);
        $this->assertDatabaseMissing('visit_cancellation_reasons', ['id' => $visitCancellationReason->id]);
    }
}