<?php

namespace Modules\GeneralReservationStatus\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\GeneralReservationStatus\Models\ReservationStatus;
use Tests\TestCase;

class ReservationStatusControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): void
    {
        $this->actingAs(User::factory()->create(), 'sanctum');
    }

    public function test_it_lists_reservation_statuse(): void
    {
        $this->actingUser();
        ReservationStatus::factory()->count(3)->create();

        $this->getJson('/api/v1/reservation-statuses')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_it_creates_reservation_status(): void
    {
        $this->actingUser();

        $this->postJson('/api/v1/reservation-statuses', ['name' => 'Contoh Statusreservasi', 'code' => 'TST'])
            ->assertCreated()
            ->assertJsonPath('name', 'Contoh Statusreservasi');

        $this->assertDatabaseHas('reservation_statuses', ['name' => 'Contoh Statusreservasi']);
    }

    public function test_it_rejects_duplicate_name(): void
    {
        $this->actingUser();
        ReservationStatus::factory()->create(['name' => 'Contoh Statusreservasi']);

        $this->postJson('/api/v1/reservation-statuses', ['name' => 'Contoh Statusreservasi'])->assertStatus(422);
    }

    public function test_it_deletes_reservation_status(): void
    {
        $this->actingUser();
        $reservationStatus = ReservationStatus::factory()->create();

        $this->deleteJson("/api/v1/reservation-statuses/{$reservationStatus->id}")->assertStatus(204);
        $this->assertDatabaseMissing('reservation_statuses', ['id' => $reservationStatus->id]);
    }
}