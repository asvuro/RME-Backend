<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\PendaftaranVisit\Models\VisitTransfer;

/**
 * Efek samping non-kritis setelah gerbang mutasi commit (jangkar audit #12).
 * Tanpa listener = no-op; alur transaksional tetap di VisitService::transfer().
 */
class VisitTransferred
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly VisitTransfer $transfer) {}
}
