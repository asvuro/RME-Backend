<?php

namespace Modules\AuditInfectionSurveillance\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\AuditInfectionSurveillance\Models\DeviceDay;
use Modules\AuditInfectionSurveillance\Models\InfectionCase;
use Modules\AuditInfectionSurveillance\Services\SurveillanceService;
use Modules\Auth\Models\User;
use Modules\PendaftaranVisit\Models\Visit;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class DeviceDayApiTest extends TestCase
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

    public function test_petugas_can_record_a_device_day(): void
    {
        $this->actingUser();
        $visit = Visit::factory()->create();

        $response = $this->postJson('/api/v1/device-days', [
            'visit_id' => $visit->id,
            'device_type' => DeviceDay::TYPE_KATETER_URINE,
            'inserted_at' => '2026-07-01 08:00:00',
            'removed_at' => '2026-07-05 09:00:00',
        ]);

        $response->assertCreated();
        $record = DeviceDay::query()->where('visit_id', $visit->id)->first();
        $this->assertNotNull($record);
        $this->assertSame(DeviceDay::TYPE_KATETER_URINE, $record->device_type);
        $this->assertSame('2026-07-05 09:00:00', $record->removed_at?->format('Y-m-d H:i:s'));
    }

    public function test_rejects_removal_before_insertion(): void
    {
        $this->actingUser();
        $visit = Visit::factory()->create();

        $this->postJson('/api/v1/device-days', [
            'visit_id' => $visit->id,
            'device_type' => DeviceDay::TYPE_KATETER_URINE,
            'inserted_at' => '2026-07-05 08:00:00',
            'removed_at' => '2026-07-01 08:00:00',
        ])->assertStatus(422);

        $this->assertDatabaseCount('device_days', 0);
    }

    /** Gerbang service dicek langsung: after_or_equal di FormRequest hanya
     * melindungi store, rentang gabungan nilai lama+baru di update hanya
     * bisa divalidasi oleh SurveillanceService::updateDeviceDay(). */
    public function test_service_gate_blocks_contradictory_window_on_update(): void
    {
        $this->actingUser();
        $deviceDay = DeviceDay::factory()->create([
            'device_type' => DeviceDay::TYPE_VENTILATOR,
            'inserted_at' => '2026-07-01 08:00:00',
            'removed_at' => '2026-07-05 09:00:00',
        ]);

        try {
            app(SurveillanceService::class)->updateDeviceDay($deviceDay, [
                'removed_at' => '2026-06-30 09:00:00',
            ]);
            $this->fail('Gerbang seharusnya menolak lepas sebelum pasang.');
        } catch (HttpException $exception) {
            $this->assertSame(422, $exception->getStatusCode());
        }

        $this->assertSame('2026-07-05 09:00:00', $deviceDay->fresh()->removed_at?->format('Y-m-d H:i:s'));
    }

    public function test_it_lists_device_days_filtered_by_visit(): void
    {
        $this->actingUser();
        $visit = Visit::factory()->create();
        DeviceDay::factory()->count(2)->create(['visit_id' => $visit->id]);
        DeviceDay::factory()->create();

        $response = $this->getJson("/api/v1/device-days?visit_id={$visit->id}");

        $response->assertOk()->assertJsonCount(2, 'data');
    }

    public function test_cannot_delete_device_day_referenced_by_an_infection_case(): void
    {
        $this->actingUser();
        $deviceDay = DeviceDay::factory()->create();
        InfectionCase::factory()->create([
            'visit_id' => $deviceDay->visit_id,
            'related_device_day_id' => $deviceDay->id,
        ]);

        $this->deleteJson("/api/v1/device-days/{$deviceDay->id}")->assertStatus(422);
        $this->assertDatabaseHas('device_days', ['id' => $deviceDay->id]);
    }
}
