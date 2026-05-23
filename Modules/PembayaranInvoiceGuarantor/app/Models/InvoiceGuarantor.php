<?php

namespace Modules\PembayaranInvoiceGuarantor\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Auth\Models\User;
use Modules\GeneralRoomClass\Models\RoomClass;
use Modules\PembayaranInvoice\Models\Invoice;
use Modules\PembayaranInvoiceGuarantor\Database\Factories\InvoiceGuarantorFactory;
use Modules\PendaftaranGuarantor\Models\Guarantor;

class InvoiceGuarantor extends Model
{
    use HasFactory;

    public const VERIFICATION_STATUSES = ['pending', 'verified', 'rejected'];

    protected $fillable = [
        'invoice_id',
        'guarantor_id',
        'covered_amount',
        // Port penjamin_tagihan simgos2: urutan lampiran (KE) dan kelas klaim.
        'sequence',
        'room_class_id',
        'coverage_percentage',
        'verification_status',
        'verified_by',
        'verified_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'covered_amount' => 'decimal:2',
            'coverage_percentage' => 'decimal:2',
            'sequence' => 'integer',
            'verified_at' => 'datetime',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function guarantor(): BelongsTo
    {
        return $this->belongsTo(Guarantor::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /** Port KELAS_KLAIM: kelas ruang yang dijadikan dasar klaim. */
    public function roomClass(): BelongsTo
    {
        return $this->belongsTo(RoomClass::class);
    }

    protected static function booted(): void
    {
        // Ala SELECT MAX(KE)+1 storePenjaminTagihan: urutan lanjutan per invoice
        // saat baris dibuat tanpa sequence eksplisit (mis. lewat CRUD flat).
        static::creating(function (self $attachment) {
            if ($attachment->sequence === null) {
                $attachment->sequence = (int) static::query()
                    ->where('invoice_id', $attachment->invoice_id)
                    ->max('sequence') + 1;
            }
        });
    }

    protected static function newFactory(): InvoiceGuarantorFactory
    {
        return InvoiceGuarantorFactory::new();
    }
}
