<?php

namespace Modules\AuditActivityLog\Listeners;

use App\Events\InvoiceLocked;
use App\Events\PrescriptionDispensed;
use App\Events\VisitAdmitted;
use App\Events\VisitDischarged;
use App\Events\VisitTransferred;
use Modules\AuditActivityLog\Models\ActivityLog;
use Modules\AuditActivityLog\Support\AuditLogger;

/**
 * Jangkar audit yang dijanjikan sejak #7: kelima milestone domain (#7–#11)
 * tercatat sebagai baris semantik action='event'. Listener ini sengaja
 * TIDAK melempar exception ke alur bisnis — jejak audit gagal tak boleh
 * membatalkan pelayanan; biarkan error handler yang mencatat.
 */
class DomainEventAuditListener
{
    public function __construct(protected AuditLogger $logger) {}

    public function handle(VisitAdmitted|VisitTransferred|VisitDischarged|InvoiceLocked|PrescriptionDispensed $event): void
    {
        match (true) {
            $event instanceof VisitAdmitted => $this->logVisit('visit_admission', [
                'visit_number' => $event->visit->visit_number,
                'ward_id' => $event->visit->ward_id,
                'bed_id' => $event->visit->bed_id,
            ], (string) $event->visit->id),
            $event instanceof VisitTransferred => $this->logVisit('visit_transfer', [
                'ward_from_id' => $event->transfer->ward_from_id,
                'bed_from_id' => $event->transfer->bed_from_id,
                'ward_to_id' => $event->transfer->ward_to_id,
                'bed_to_id' => $event->transfer->bed_to_id,
            ], (string) $event->transfer->id),
            $event instanceof VisitDischarged => $this->logVisit('visit_discharge', [
                'final_outcome' => $event->visit->final_outcome,
                'discharged_at' => $event->visit->discharged_at?->toIso8601String(),
            ], (string) $event->visit->id),
            $event instanceof InvoiceLocked => $this->logBilling('invoice_lock', [
                'invoice_number' => $event->invoice->invoice_number,
                'total_amount' => $event->invoice->total_amount,
            ], (string) $event->invoice->id),
            $event instanceof PrescriptionDispensed => $this->logPharmacy([
                'prescription_id' => $event->dispense->prescription_id,
                'quantity' => $event->dispense->quantity,
            ], (string) $event->dispense->id),
        };
    }

    protected function logVisit(string $object, array $after, string $ref): void
    {
        $this->logger->log(ActivityLog::ACTION_EVENT, $object, $ref, null, $after);
    }

    protected function logBilling(string $object, array $after, string $ref): void
    {
        $this->logger->log(ActivityLog::ACTION_EVENT, $object, $ref, null, $after);
    }

    protected function logPharmacy(array $after, string $ref): void
    {
        $this->logger->log(ActivityLog::ACTION_EVENT, 'prescription_dispense', $ref, null, $after);
    }
}
