<?php

namespace Modules\GeneralPlanningPeriod\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralPlanningPeriod\Models\PlanningPeriod;
use Tests\TestCase;

class PlanningPeriodControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_planning_period(): void
    {
        $this->actingUser();
        PlanningPeriod::factory()->count(3)->create();

        $this->getJson('/api/v1/planning-periods')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_planning_period(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/planning-periods', ['name' => 'Contoh Periodeperencanaan', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Periodeperencanaan');

        $this->assertDatabaseHas('planning_periods', ['name' => 'Contoh Periodeperencanaan']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        PlanningPeriod::factory()->create(['name' => 'Contoh Periodeperencanaan']);

        $this->postJson('/api/v1/planning-periods', ['name' => 'Contoh Periodeperencanaan'])->assertStatus(422);
    }

    public function test_it_deletes_planning_period(): void
    {
        $this->actingUser();
        $planningPeriod = PlanningPeriod::factory()->create();

        $this->deleteJson("/api/v1/planning-periods/{$planningPeriod->id}")->assertStatus(204);
        $this->assertDatabaseMissing('planning_periods', ['id' => $planningPeriod->id]);
    }
}