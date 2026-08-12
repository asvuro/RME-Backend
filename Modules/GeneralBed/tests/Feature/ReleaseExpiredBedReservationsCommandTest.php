<?php

namespace Modules\GeneralBed\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\GeneralBed\Models\Bed;
use Tests\TestCase;

class ReleaseExpiredBedReservationsCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_melepas_reservasi_kedaluwarsa(): void
    {
        $expired = Bed::factory()->create(['status' => Bed::STATUS_RESERVED, 'reserved_until' => now()->subMinute()]);
        $stillValid = Bed::factory()->create(['status' => Bed::STATUS_RESERVED, 'reserved_until' => now()->addMinutes(30)]);

        $this->artisan('bed:release-expired-reservations')
            ->assertExitCode(0);

        $this->assertSame(Bed::STATUS_AVAILABLE, $expired->refresh()->status);
        $this->assertSame(Bed::STATUS_RESERVED, $stillValid->refresh()->status);
    }
}
