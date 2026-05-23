<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\PendaftaranVisit\Models\Visit;

/**
 * Efek samping non-kritis setelah gerbang pulang commit: bed sudah bebas,
 * rekam pulang terbentuk, akomodasi terposting (jangkar audit #12).
 */
class VisitDischarged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly Visit $visit) {}
}
