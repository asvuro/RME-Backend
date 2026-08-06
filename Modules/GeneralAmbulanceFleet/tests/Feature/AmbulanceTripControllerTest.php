<?php

namespace Modules\GeneralAmbulanceFleet\Tests\Feature;

use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralAmbulanceFleet\Models\Ambulance;
use Modules\GeneralAmbulanceFleet\Models\AmbulanceTrip;
use Modules\GeneralEmployee\Models\Employee;
use Modules\GeneralPatient\Models\Patient;
use Tests\TestCase;

class AmbulanceTripControllerTest extends TestCase
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

    /**
     * @param  Ambulance|null  $ambulance  ambulans yang dipakai; default buat baru (available)
     * @return array<string, mixed>
     */
    private function tripPayload(?Ambulance $ambulance = null): array
    {
        return [
            'ambulance_id' => ($ambulance ?? Ambulance::factory()->create())->id,
            'patient_id' => Patient::factory()->create()->id,
            'driver_employee_id' => Employee::factory()->create()->id,
            'purpose' => AmbulanceTrip::PURPOSE_RUJUKAN_KELUAR,
            'origin' => 'RS Umum Daerah',
            'destination' => 'RS Rujukan Nasional',
            'departed_at' => '2026-08-26 08:00:00',
        ];
    }

    public function test_it_lists_ambulance_trips(): void
    {
        $this->actingUser();
        AmbulanceTrip::factory()->count(3)->create();

        $this->getJson('/api/v1/ambulance-trips')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_petugas_can_start_trip_and_ambulance_becomes_in_use(): void
    {
        $this->actingUser();
        $payload = $this->tripPayload();

        $tripId = $this->postJson('/api/v1/ambulance-trips', $payload)
            ->assertCreated()
            ->assertJsonPath('status', AmbulanceTrip::STATUS_ONGOING)
            ->json('id');

        $this->assertDatabaseHas('ambulance_trips', [
            'id' => $tripId,
            'status' => AmbulanceTrip::STATUS_ONGOING,
            'patient_id' => $payload['patient_id'],
        ]);

        // Efek samping inti: mulai trip harus mengunci ambulans ke in_use.
        $this->assertDatabaseHas('ambulances', [
            'id' => $payload['ambulance_id'],
            'status' => Ambulance::STATUS_IN_USE,
        ]);
    }

    public function test_start_is_rejected_when_ambulance_already_in_use(): void
    {
        $this->actingUser();
        $busy = Ambulance::factory()->inUse()->create();

        // Satu ambulans hanya boleh satu trip aktif - kalau tidak, dua trip
        // bisa "menyelesaikan" ambulans yang sama secara bersamaan.
        $this->postJson('/api/v1/ambulance-trips', $this->tripPayload($busy))
            ->assertStatus(422);
    }

    public function test_start_is_rejected_during_maintenance(): void
    {
        $this->actingUser();
        $serviced = Ambulance::factory()->maintenance()->create();

        $this->postJson('/api/v1/ambulance-trips', $this->tripPayload($serviced))
            ->assertStatus(422);
    }

    public function test_complete_trip_releases_the_ambulance(): void
    {
        $this->actingUser();
        $payload = $this->tripPayload();
        $tripId = $this->postJson('/api/v1/ambulance-trips', $payload)->assertCreated()->json('id');

        $this->postJson("/api/v1/ambulance-trips/{$tripId}/complete", [
            'returned_at' => '2026-08-26 10:30:00',
        ])
            ->assertOk()
            ->assertJsonPath('status', AmbulanceTrip::STATUS_COMPLETED);

        $trip = AmbulanceTrip::query()->findOrFail($tripId);
        $this->assertSame('2026-08-26 10:30:00', $trip->returned_at?->format('Y-m-d H:i:s'));

        // Selesai trip adalah satu-satunya jalan ambulans kembali available.
        $this->assertDatabaseHas('ambulances', [
            'id' => $payload['ambulance_id'],
            'status' => Ambulance::STATUS_AVAILABLE,
        ]);
    }

    public function test_complete_is_rejected_for_non_ongoing_trip(): void
    {
        $this->actingUser();
        $trip = AmbulanceTrip::factory()->completed()->create();

        $this->postJson("/api/v1/ambulance-trips/{$trip->id}/complete")
            ->assertStatus(422);
    }
}
