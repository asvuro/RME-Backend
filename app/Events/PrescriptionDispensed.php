<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\LayananPharmacyDispense\Models\PharmacyDispense;

/**
 * Resep selesai dilayani farmasi (port finalPelayananFarmasi STATUS=2 simgos2).
 * Efek samping non-kritis setelah commit (audit trail #12 nanti memakainya).
 */
class PrescriptionDispensed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly PharmacyDispense $dispense) {}
}
