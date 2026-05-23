<?php

namespace Modules\GeneralVisitActivityStatus\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralVisitActivityStatus\Models\VisitActivityStatus;
use Tests\TestCase;

class VisitActivityStatusControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_visit_activity_statuse(): void
    {
        $this->actingUser();
        VisitActivityStatus::factory()->count(3)->create();

        $this->getJson('/api/v1/visit-activity-statuses')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_visit_activity_status(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/visit-activity-statuses', ['name' => 'Contoh Statusaktifitaskunjungan', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Statusaktifitaskunjungan');

        $this->assertDatabaseHas('visit_activity_statuses', ['name' => 'Contoh Statusaktifitaskunjungan']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        VisitActivityStatus::factory()->create(['name' => 'Contoh Statusaktifitaskunjungan']);

        $this->postJson('/api/v1/visit-activity-statuses', ['name' => 'Contoh Statusaktifitaskunjungan'])->assertStatus(422);
    }

    public function test_it_deletes_visit_activity_status(): void
    {
        $this->actingUser();
        $visitActivityStatus = VisitActivityStatus::factory()->create();

        $this->deleteJson("/api/v1/visit-activity-statuses/{$visitActivityStatus->id}")->assertStatus(204);
        $this->assertDatabaseMissing('visit_activity_statuses', ['id' => $visitActivityStatus->id]);
    }
}