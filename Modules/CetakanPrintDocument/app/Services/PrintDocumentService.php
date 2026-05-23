<?php

namespace Modules\CetakanPrintDocument\Services;

use App\Events\PrintDocumentIssued;
use App\Modules\Contracts\HospitalConfig;
use Illuminate\Support\Facades\DB;
use Modules\Auth\Models\User;
use Modules\CetakanPrintDocument\Models\PrintDocument;
use Modules\PembayaranInvoice\Models\Invoice;
use Modules\PembayaranPayment\Models\Payment;
use Modules\PendaftaranRegistration\Models\Registration;
use Modules\PendaftaranVisit\Models\Visit;

/**
 * Penerbitan dokumen cetak bernomor seri — port cetakan.storeKarcis
 * (idempoten: satu dokumen per jenis+referensi, penerbitan ulang hanya
 * menyegarkan snapshot payload) dan semangat kwitansi_pembayaran.
 */
class PrintDocumentService
{
    /** Jenis dokumen → referensi yang sah untuk diterbitkan. */
    private const REF_MAP = [
        PrintDocument::TYPE_RECEIPT => 'payments',
        PrintDocument::TYPE_KARCIS => 'registrations',
        PrintDocument::TYPE_WRISTBAND => 'visits',
        PrintDocument::TYPE_TRACER => 'registrations',
    ];

    public function __construct(private readonly HospitalConfig $config)
    {
    }

    /**
     * Terbitkan (atau terbitkan-ulang payload segar) satu dokumen.
     *
     * @return array{document: PrintDocument, reused: bool}
     */
    public function issue(string $type, string $refType, int $refId, ?User $user): array
    {
        abort_if(! isset(self::REF_MAP[$type]), 422, "Jenis dokumen '{$type}' tidak dikenal.");
        abort_if(self::REF_MAP[$type] !== $refType, 422,
            "Dokumen {$type} menerbitkan atas referensi ".self::REF_MAP[$type].", bukan {$refType}.");

        // Gerbang konfigurasi ala PropertiConfig 12/25/29.
        if ($type === PrintDocument::TYPE_WRISTBAND && ! $this->config->get('printing.print_wristband', true)) {
            abort(422, 'Cetakan gelang dimatikan lewat konfigurasi RS.');
        }
        if ($type === PrintDocument::TYPE_TRACER) {
            abort_if(! $this->config->get('printing.auto_print_tracer', true), 422,
                'Cetakan tracer dimatikan lewat konfigurasi RS.');
            if (! $this->config->get('printing.allow_tracer_inpatient', false)
                && $this->registrationHasInpatientVisit($refId)) {
                abort(422, 'Tracer untuk pasien rawat inap tidak diizinkan konfigurasi.');
            }
        }

        [$document, $reused] = DB::transaction(function () use ($type, $refType, $refId, $user): array {
            $existing = PrintDocument::query()
                ->where('document_type', $type)
                ->where('ref_type', $refType)
                ->where('ref_id', $refId)
                ->lockForUpdate()
                ->first();

            if ($existing === null) {
                $payload = $this->buildPayload($type, $refId);
                $existing = PrintDocument::query()->create([
                    'document_type' => $type,
                    'ref_type' => $refType,
                    'ref_id' => $refId,
                    'document_number' => PrintDocument::generateDocumentNumber($type),
                    'payload' => $payload,
                    'issued_by' => $user?->id,
                    'issued_at' => now(),
                ]);

                return [$existing, false];
            }

            // Idempoten ala storeKarcis — baris lama tetap, payload disegarkan.
            $existing->update(['payload' => $this->buildPayload($type, $refId)]);

            return [$existing->refresh(), true];
        });

        if (! $reused) {
            PrintDocumentIssued::dispatch($document);
        }

        return ['document' => $document, 'reused' => $reused];
    }

    private function buildPayload(string $type, int $refId): array
    {
        return match ($type) {
            PrintDocument::TYPE_RECEIPT => $this->receiptPayload($refId),
            PrintDocument::TYPE_KARCIS => $this->registrationPayload($refId),
            PrintDocument::TYPE_TRACER => $this->registrationPayload($refId),
            PrintDocument::TYPE_WRISTBAND => $this->wristbandPayload($refId),
        };
    }

    /** Kwitansi pembayaran — padan kwitansi_pembayaran (nomor seri, tagihan, penyetor). */
    private function receiptPayload(int $paymentId): array
    {
        $payment = Payment::query()
            ->with(['invoice.visit.registration.patient.gender', 'receivedBy'])
            ->findOrFail($paymentId);

        return [
            'title' => 'KWITANSI PEMBAYARAN',
            'payment_number' => $payment->payment_number,
            'payment_method' => $payment->payment_method,
            'amount' => $payment->amount,
            'admin_fee' => $payment->admin_fee,
            'paid_at' => optional($payment->paid_at)->toDateTimeString(),
            'received_by' => $payment->receivedBy?->name,
            'payer_name' => $this->patientName($payment->invoice),
            'invoice' => [
                'invoice_number' => $payment->invoice->invoice_number,
                'total_amount' => $payment->invoice->total_amount,
                'patient_share' => $payment->invoice->patient_share,
            ],
        ];
    }

    /** Karcis/tracer pendaftaran — padan karcis_pasien. */
    private function registrationPayload(int $registrationId): array
    {
        $registration = Registration::query()
            ->with(['patient.gender', 'registeredBy'])
            ->findOrFail($registrationId);

        $inpatientWard = Visit::query()
            ->where('registration_id', $registration->id)
            ->whereNotNull('ward_id')
            ->with('ward:id,name')
            ->first();

        return [
            'title' => 'KARCIS PASIEN',
            'registration_number' => $registration->registration_number,
            'registered_at' => optional($registration->registered_at)->toDateTimeString(),
            'status' => $registration->status,
            'petugas' => $registration->registeredBy?->name,
            'tujuan' => $inpatientWard?->ward?->name,
            'patient' => $this->patientArray($registration),
        ];
    }

    /** Gelang identitas — data minimum pasien + lokasi rawat. */
    private function wristbandPayload(int $visitId): array
    {
        $visit = Visit::query()
            ->with(['registration.patient.gender', 'ward:id,name', 'bed.room:id,class_id'])
            ->findOrFail($visitId);

        return [
            'title' => 'GELANG IDENTITAS PASIEN',
            'visit_number' => $visit->visit_number,
            'admitted_at' => optional($visit->admitted_at)->toDateTimeString(),
            'ward' => $visit->ward?->name,
            'bed' => $visit->bed?->bed_number,
            'patient' => $this->patientArray($visit->registration),
        ];
    }

    private function registrationHasInpatientVisit(int $registrationId): bool
    {
        return Visit::query()
            ->where('registration_id', $registrationId)
            ->whereNotNull('ward_id')
            ->exists();
    }

    /** @return array<string, mixed>|null */
    private function patientArray(?Registration $registration): ?array
    {
        $patient = $registration?->patient;
        if ($patient === null) {
            return null;
        }

        return [
            'medical_record_number' => $patient->medical_record_number,
            'name' => $patient->name,
            'birth_date' => optional($patient->birth_date)->toDateString(),
            'gender' => $patient->gender?->name,
        ];
    }

    private function patientName(Invoice $invoice): ?string
    {
        return $invoice->visit?->registration?->patient?->name;
    }
}
