<?php

namespace Modules\AuditInfectionSurveillance\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\AuditInfectionSurveillance\Models\DeviceDay;
use Modules\AuditInfectionSurveillance\Models\InfectionCase;
use Modules\Auth\Models\User;
use Modules\PendaftaranVisit\Models\Visit;
use Tests\TestCase;

class SurveillanceRateTest extends TestCase
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

    /** Skenario angka eksak, periode 1-31 Juli 2026, jenis ISK:
     *  - kateter 1: pasang 1 Jul, lepas 11 Jul -> hari-alat = 11 (inklusif);
     *  - kateter 2: pasang 20 Jul, masih terpasang -> dihitung s.d. 31 Jul = 12;
     *  - ventilator 2-28 Jul (29 hari) TIDAK masuk denominator ISK;
     *  - kasus ISK 15 Jul dihitung, kasus ISK 30 Jun dan kasus VAP tidak.
     *  Denominator = 23; rate = (1/23) x 1000 = 43.48. */
    public function test_rate_formula_counts_matching_devices_and_period_cases(): void
    {
        $visit = Visit::factory()->create();

        DeviceDay::factory()->create([
            'visit_id' => $visit->id,
            'device_type' => DeviceDay::TYPE_KATETER_URINE,
            'inserted_at' => '2026-07-01 08:00:00',
            'removed_at' => '2026-07-11 10:00:00',
        ]);
        DeviceDay::factory()->create([
            'visit_id' => $visit->id,
            'device_type' => DeviceDay::TYPE_KATETER_URINE,
            'inserted_at' => '2026-07-20 09:00:00',
            'removed_at' => null,
        ]);
        DeviceDay::factory()->create([
            'visit_id' => $visit->id,
            'device_type' => DeviceDay::TYPE_VENTILATOR,
            'inserted_at' => '2026-07-02 08:00:00',
            'removed_at' => '2026-07-28 08:00:00',
        ]);

        InfectionCase::factory()->create([
            'visit_id' => $visit->id,
            'infection_type' => InfectionCase::TYPE_ISK,
            'diagnosed_at' => '2026-07-15 12:00:00',
        ]);
        InfectionCase::factory()->create([
            'visit_id' => $visit->id,
            'infection_type' => InfectionCase::TYPE_ISK,
            'diagnosed_at' => '2026-06-30 12:00:00', // luar periode.
        ]);
        InfectionCase::factory()->create([
            'visit_id' => $visit->id,
            'infection_type' => InfectionCase::TYPE_VAP,
            'diagnosed_at' => '2026-07-15 12:00:00', // jenis lain.
        ]);

        $response = $this->getJson('/api/v1/infection-surveillance/rate?type=ISK&start=2026-07-01&end=2026-07-31');

        $response->assertOk();
        $this->assertSame(1, $response->json('cases'));
        $this->assertSame(23, $response->json('deviceDays'));
        $this->assertEqualsWithDelta(43.48, $response->json('ratePer1000'), 0.001);
    }

    /** Pembagi nol harus menghasilkan rate 0, bukan division-by-zero/INF. */
    public function test_zero_denominator_returns_zero_rate(): void
    {
        $visit = Visit::factory()->create();
        InfectionCase::factory()->create([
            'visit_id' => $visit->id,
            'infection_type' => InfectionCase::TYPE_VAP,
            'diagnosed_at' => '2026-07-15 12:00:00',
        ]);
        // Tidak ada hari-alat ventilator sama sekali pada periode.

        $response = $this->getJson('/api/v1/infection-surveillance/rate?type=VAP&start=2026-07-01&end=2026-07-31');

        $response->assertOk();
        $this->assertSame(1, $response->json('cases'));
        $this->assertSame(0, $response->json('deviceDays'));
        $this->assertSame(0, $response->json('ratePer1000'));
    }

    public function test_unknown_infection_type_is_rejected(): void
    {
        $this->getJson('/api/v1/infection-surveillance/rate?type=bakteremi&start=2026-07-01&end=2026-07-31')
            ->assertStatus(422);
    }
}
