<?php

namespace Modules\AuditInfectionSurveillance\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\AuditInfectionSurveillance\Models\DeviceDay;
use Modules\AuditInfectionSurveillance\Models\InfectionCase;
use Modules\Auth\Models\User;
use Modules\PendaftaranVisit\Models\Visit;
use Tests\TestCase;

class InfectionCaseApiTest extends TestCase
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

    public function test_petugas_can_report_an_infection_case(): void
    {
        $this->actingUser();
        $visit = Visit::factory()->create();
        $deviceDay = DeviceDay::factory()->create([
            'visit_id' => $visit->id,
            'device_type' => DeviceDay::TYPE_KATETER_URINE,
        ]);

        $response = $this->postJson('/api/v1/infection-cases', [
            'visit_id' => $visit->id,
            'infection_type' => InfectionCase::TYPE_ISK,
            'diagnosed_at' => '2026-07-15 12:00:00',
            'related_device_day_id' => $deviceDay->id,
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('infection_cases', [
            'visit_id' => $visit->id,
            'infection_type' => InfectionCase::TYPE_ISK,
            'related_device_day_id' => $deviceDay->id,
        ]);
    }

    public function test_rejects_case_referencing_device_day_of_another_visit(): void
    {
        $this->actingUser();
        $visit = Visit::factory()->create();
        $deviceDayLain = DeviceDay::factory()->create(); // kunjungan lain dari factory-nya sendiri

        $this->postJson('/api/v1/infection-cases', [
            'visit_id' => $visit->id,
            'infection_type' => InfectionCase::TYPE_ISK,
            'diagnosed_at' => '2026-07-15 12:00:00',
            'related_device_day_id' => $deviceDayLain->id,
        ])->assertStatus(422);

        $this->assertDatabaseCount('infection_cases', 0);
    }

    public function test_rejects_device_type_that_does_not_match_the_infection_type(): void
    {
        $this->actingUser();
        $visit = Visit::factory()->create();
        $ventilatorDay = DeviceDay::factory()->create([
            'visit_id' => $visit->id,
            'device_type' => DeviceDay::TYPE_VENTILATOR,
        ]);

        // Kasus ISK harus merujuk kateter urin, bukan hari-alat ventilator.
        $this->postJson('/api/v1/infection-cases', [
            'visit_id' => $visit->id,
            'infection_type' => InfectionCase::TYPE_ISK,
            'diagnosed_at' => '2026-07-15 12:00:00',
            'related_device_day_id' => $ventilatorDay->id,
        ])->assertStatus(422);
    }

    public function test_it_lists_infection_cases_filtered_by_type(): void
    {
        $this->actingUser();
        InfectionCase::factory()->count(2)->create(['infection_type' => InfectionCase::TYPE_ISK]);
        InfectionCase::factory()->create(['infection_type' => InfectionCase::TYPE_VAP]);

        $response = $this->getJson('/api/v1/infection-cases?infection_type='.InfectionCase::TYPE_ISK);

        $response->assertOk()->assertJsonCount(2, 'data');
    }
}
