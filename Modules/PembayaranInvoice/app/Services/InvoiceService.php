<?php

namespace Modules\PembayaranInvoice\Services;

use App\Events\InvoiceLocked;
use App\Modules\Contracts\BillingGate;
use Illuminate\Support\Facades\DB;
use Modules\PembayaranInvoice\Models\Invoice;
use Modules\PembayaranInvoiceGuarantor\Models\InvoiceGuarantor;
use Modules\PembayaranInvoiceItem\Models\InvoiceItem;
use Modules\PendaftaranGuarantor\Models\Guarantor;
use Modules\PendaftaranVisit\Models\Visit;

/**
 * Domain service mesin tagihan — port rutin `pembayaran.*` simgos2 ke kode
 * Laravel testable (aslinya stored procedure MySQL).
 *
 * Yang di-port di tahap ini (#9):
 * - buatTagihan            → ensureForVisit()
 * - storePenjaminTagihan   → attachGuarantor() (lampiran idempoten, sequence ala KE)
 * - reProsesDistribusiTarif→ redistribute() (reset-then-recompute)
 * - getTotalPenjaminTagihan→ coverage() / Invoice::coveredAmount()
 * - STATUS=2 final         → lock()/unlock() + event InvoiceLocked
 *
 * Bucket agregasi INACBG & mesin naik-kelas prosesPerhitunganBPJS sengaja
 * disingkat: itu concern pengajuan klaim BPJS yang bergantung hasil_grouping,
 * bukan core billing (lihat komentar migrasi invoice_items).
 */
class InvoiceService implements BillingGate
{
    /**
     * Gerbang locked SELALU membaca state DB (bukan atribut instance yang
     * mungkin basi di proses long-running), ala SP simgos2 yang membaca
     * tabel langsung - sekaligus menutup race dengan lock dari request lain.
     */
    protected function isInvoiceLocked(int $invoiceId): bool
    {
        return Invoice::query()->whereKey($invoiceId)->where('is_locked', true)->exists();
    }

    /**
     * Kunci invoice (kasir menutup pembayaran). Ala config 69 simgos2:
     * setelah terkunci, tidak ada posting layanan baru ke kunjungan tsb.
     */
    public function lock(int $invoiceId): void
    {
        $invoice = Invoice::findOrFail($invoiceId);

        DB::transaction(function () use ($invoice) {
            $invoice->update(['is_locked' => true]);
        });

        // Event di luar transaksi: listener hanya boleh menyentuh data yang
        // sudah commit (efek samping non-kritis; audit menyusul #12).
        InvoiceLocked::dispatch($invoice->refresh());
    }

    public function unlock(int $invoiceId): void
    {
        Invoice::query()->whereKey($invoiceId)->update(['is_locked' => false]);
    }

    /**
     * TRUE bila ada invoice kunjungan ini yang is_locked=true.
     * Dipanggil gerbang admission/service lain via App\Modules\Contracts\BillingGate.
     */
    public function isVisitLocked(int $visitId): bool
    {
        return Invoice::query()
            ->where('visit_id', $visitId)
            ->where('is_locked', true)
            ->exists();
    }

    /**
     * Port pembayaran.buatTagihan: get-or-create satu invoice terbuka untuk
     * kunjungan. RME menyederhanakan model multi-pendaftaran simgos2 menjadi
     * 1 invoice : 1 kunjungan.
     */
    public function ensureForVisit(int $visitId): Invoice
    {
        Visit::findOrFail($visitId);

        $invoice = Invoice::query()->firstOrCreate(
            ['visit_id' => $visitId],
            [
                'invoice_number' => Invoice::generateInvoiceNumber(),
                'invoice_date' => now(),
            ],
        );

        return $invoice->refresh();
    }

    /**
     * Port pembayaran.storePenjaminTagihan: lampirkan penjamin ke invoice,
     * idempoten (satu penjamin satu baris), sequence berurutan ala KE -
     * sequence 1 adalah penanggung utama (ala farmasi simgos2 yang membaca KE=1).
     *
     * @throws HttpException bila invoice terkunci.
     */
    public function attachGuarantor(Invoice $invoice, int $guarantorId, ?int $roomClassId = null): InvoiceGuarantor
    {
        // Pola gerbang #7 (VisitService::abort_if): pesan JSON {"message": ...}.
        abort_if($this->isInvoiceLocked($invoice->id), 422, 'Tagihan sudah dikunci, distribusi penjamin tidak bisa diubah.');

        $guarantor = Guarantor::query()->findOrFail($guarantorId);

        return DB::transaction(function () use ($invoice, $guarantor, $roomClassId) {
            $existing = InvoiceGuarantor::query()
                ->where('invoice_id', $invoice->id)
                ->where('guarantor_id', $guarantor->id)
                ->first();

            if ($existing !== null) {
                return $existing;
            }

            // Ala SELECT MAX(KE)+1: urutan lanjutan penjamin pada invoice ini.
            $nextSequence = (int) InvoiceGuarantor::query()
                ->where('invoice_id', $invoice->id)
                ->max('sequence') + 1;

            return InvoiceGuarantor::create([
                'invoice_id' => $invoice->id,
                'guarantor_id' => $guarantor->id,
                'sequence' => $nextSequence,
                'covered_amount' => 0,
                'room_class_id' => $roomClassId ?? $guarantor->room_class_id,
            ]);
        });
    }

    /**
     * Port pembayaran.reProsesDistribusiTarif: hitung ulang distribusi dari nol
     * (reset-then-recompute), idempoten.
     *
     * Sumber baris = guarantor AKTIF milik registration kunjungan, urut id agar
     * deterministik. Aturan tanggungan versi inti: penjamin non-self_pay PERTAMA
     * menanggung seluruh total (ala TOTAL BPJS = nilai klaim); tanpa penjamin
     * non-self_pay seluruh tagihan menjadi tanggungan pasien (covered 0).
     */
    public function redistribute(Invoice $invoice): void
    {
        abort_if($this->isInvoiceLocked($invoice->id), 422, 'Tagihan sudah dikunci, distribusi penjamin tidak bisa diubah.');

        $visit = $invoice->visit()->firstOrFail();

        $activeGuarantors = Guarantor::query()
            ->where('registration_id', $visit->registration_id)
            ->where('status', 'active')
            ->orderBy('id')
            ->get();

        DB::transaction(function () use ($invoice, $activeGuarantors) {
            // RESET: sinkronkan baris lampiran dengan daftar guarantor aktif.
            $attachments = collect();
            foreach ($activeGuarantors as $index => $guarantor) {
                $attachments[] = InvoiceGuarantor::query()->updateOrCreate(
                    ['invoice_id' => $invoice->id, 'guarantor_id' => $guarantor->id],
                    ['sequence' => $index + 1, 'room_class_id' => $guarantor->room_class_id],
                );
            }

            // Baris lampiran milik guarantor yang sudah tidak aktif ikut dibuang.
            InvoiceGuarantor::query()
                ->where('invoice_id', $invoice->id)
                ->when($activeGuarantors->isNotEmpty(), fn ($q) => $q->whereNotIn('guarantor_id', $activeGuarantors->pluck('id')))
                ->delete();

            // RECOMPUTE: penjamin non-self_pay pertama menanggung penuh.
            $payer = $attachments->first(fn (InvoiceGuarantor $a) => $a->guarantor->payer_type !== 'self_pay');

            foreach ($attachments as $attachment) {
                $attachment->update([
                    'covered_amount' => $attachment->is($payer) ? $invoice->total_amount : 0,
                ]);
            }
        });
    }

    /**
     * Port pembayaran.getTotalPenjaminTagihan + selisihnya: ringkasan siapa
     * menanggung apa. patient_share selalu dihitung, tidak pernah disimpan.
     *
     * @return array{total: string, covered: string, patient_share: string}
     */
    public function coverage(Invoice $invoice): array
    {
        return [
            'total' => number_format((float) $invoice->total_amount, 2, '.', ''),
            'covered' => $invoice->coveredAmount(),
            'patient_share' => $invoice->patient_share,
        ];
    }

    /**
     * Recalculate subtotal/total invoice dalam transaksi (reuse
     * Invoice::recalculateTotals). Dipakai setelah posting item.
     */
    public function recalculate(Invoice $invoice): Invoice
    {
        return DB::transaction(function () use ($invoice) {
            $invoice->recalculateTotals();

            return $invoice->refresh();
        });
    }

    /**
     * Port pembayaran.storeRincianTagihan untuk modul klinis (farmasi, lab,
     * radiologi nanti): posting satu baris layanan ke invoice kunjungan via
     * kontrak BillingGate - get-or-create invoice, tambah item, recalculate,
     * lalu redistribute penjamin agar coverage tetap konsisten.
     *
     * @throws HttpException bila tagihan terkunci.
     */
    public function postServiceItem(int $visitId, string $description, ?string $category, int $quantity, float $unitPrice): void
    {
        $invoice = $this->ensureForVisit($visitId);

        abort_if($this->isInvoiceLocked($invoice->id), 422, 'Tagihan sudah dikunci, posting layanan baru ditolak.');

        DB::transaction(function () use ($invoice, $description, $category, $quantity, $unitPrice) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => $description,
                'category' => $category,
                'quantity' => max(1, $quantity),
                'unit_price' => $unitPrice,
                'subtotal' => round(max(1, $quantity) * $unitPrice, 2),
            ]);

            $invoice->recalculateTotals();
        });

        // Coverage penjamin dihitung ulang atas total terbaru.
        $this->redistribute($invoice);
    }
}
