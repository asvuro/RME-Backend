<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\PendaftaranVisit\Models\Visit;

/**
 * Efek samping non-kritis setelah admit sukses (audit trail #12 nanti
 * memakainya). Tanpa listener = no-op; alur transaksional tetap di VisitService.
 */
class VisitAdmitted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly Visit $visit) {}
}
