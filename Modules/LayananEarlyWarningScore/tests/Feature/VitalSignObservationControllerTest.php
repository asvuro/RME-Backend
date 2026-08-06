<?php

namespace Modules\LayananEarlyWarningScore\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralEmployee\Models\Employee;
use Modules\LayananEarlyWarningScore\Models\VitalSignObservation;
use Modules\PendaftaranVisit\Models\Visit;
use Tests\TestCase;

class VitalSignObservationControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);
    }

    private function actingUser(): void
    {
        $user = User::factory()->create();
        $user->assignRole('petugas');
        $this->actingAs($user, 'sanctum');
    }

    /** Skor 24/95/105/95/alert/38.4 => 2+1+1+1+0+1 = total 6, tinggi. */
    private function measuredPayload(Visit $visit, Employee $employee): array
    {
        return [
            'visit_id' => $visit->id,
            'respiratory_rate' => 24,
            'spo2' => 95,
            'systolic_bp' => 105,
            'pulse_rate' => 95,
            'consciousness_level' => 'alert',
            'temperature_celsius' => 38.4,
            'recorded_by' => $employee->id,
            // Dikirim sengaja: harus diabaikan, skor hanya boleh dari kalkulator.
            'total_score' => 99,
            'risk_level' => 'rendah',
        ];
    }

    public function test_petugas_can_store_observation_and_score_is_auto_calculated(): void
    {
        $this->actingUser();

        $visit = Visit::factory()->create();
        $employee = Employee::factory()->create();

        $this->postJson('/api/v1/vital-sign-observations', $this->measuredPayload($visit, $employee))
            ->assertCreated()
            ->assertJsonPath('data.total_score', 6)
            ->assertJsonPath('data.risk_level', 'tinggi');

        $this->assertDatabaseHas('vital_sign_observations', [
            'visit_id' => $visit->id,
            'total_score' => 6,
            // Buktikan input klien (risk_level "rendah") benar-benar diabaikan.
            'risk_level' => 'tinggi',
        ]);
    }

    public function test_store_rejects_invalid_consciousness_level(): void
    {
        $this->actingUser();

        $payload = $this->measuredPayload(Visit::factory()->create(), Employee::factory()->create());
        $payload['consciousness_level'] = 'coma';

        $this->postJson('/api/v1/vital-sign-observations', $payload)->assertUnprocessable();

        $this->assertDatabaseCount('vital_sign_observations', 0);
    }

    public function test_it_lists_observations_and_can_filter_by_visit(): void
    {
        $this->actingUser();

        VitalSignObservation::factory()->count(3)->create();
        $target = VitalSignObservation::factory()->create();

        $this->getJson('/api/v1/vital-sign-observations')
            ->assertOk()
            ->assertJsonCount(4, 'data');

        $this->getJson("/api/v1/vital-sign-observations?visit_id={$target->visit_id}")
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $target->id);
    }

    public function test_it_shows_one_observation(): void
    {
        $this->actingUser();

        $observation = VitalSignObservation::factory()->create();

        $this->getJson("/api/v1/vital-sign-observations/{$observation->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $observation->id);
    }
}
