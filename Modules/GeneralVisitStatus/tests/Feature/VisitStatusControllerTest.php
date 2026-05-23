<?php

namespace Modules\GeneralVisitStatus\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralVisitStatus\Models\VisitStatus;
use Tests\TestCase;

class VisitStatusControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_visit_statuse(): void
    {
        $this->actingUser();
        VisitStatus::factory()->count(3)->create();

        $this->getJson('/api/v1/visit-statuses')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_visit_status(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/visit-statuses', ['name' => 'Contoh Statuskunjungan', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Statuskunjungan');

        $this->assertDatabaseHas('visit_statuses', ['name' => 'Contoh Statuskunjungan']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        VisitStatus::factory()->create(['name' => 'Contoh Statuskunjungan']);

        $this->postJson('/api/v1/visit-statuses', ['name' => 'Contoh Statuskunjungan'])->assertStatus(422);
    }

    public function test_it_deletes_visit_status(): void
    {
        $this->actingUser();
        $visitStatus = VisitStatus::factory()->create();

        $this->deleteJson("/api/v1/visit-statuses/{$visitStatus->id}")->assertStatus(204);
        $this->assertDatabaseMissing('visit_statuses', ['id' => $visitStatus->id]);
    }
}