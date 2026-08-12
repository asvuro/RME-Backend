<?php

namespace Modules\GeneralBed\Console\Commands;

use Illuminate\Console\Command;
use Modules\GeneralBed\Services\BedService;

class ReleaseExpiredBedReservationsCommand extends Command
{
    protected $signature = 'bed:release-expired-reservations';

    protected $description = 'Lepas reservasi bed (status reserved) yang sudah lewat reserved_until';

    public function handle(BedService $service): int
    {
        $released = $service->releaseExpiredReservations();

        $this->info("Melepas {$released} reservasi bed yang sudah kedaluwarsa.");

        return self::SUCCESS;
    }
}
