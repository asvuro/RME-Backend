<?php

namespace Modules\PendaftaranVisit\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Auth\Models\User;
use Modules\GeneralBed\Models\Bed;
use Modules\GeneralWard\Models\Ward;

/**
 * Port pendaftaran.mutasi simgos2: jejak perpindahan pasien antar bed/ward.
 * Ditulis HANYA oleh VisitService::transfer().
 */
class VisitTransfer extends Model
{
    protected $fillable = [
        'visit_id',
        'ward_from_id',
        'bed_from_id',
        'ward_to_id',
        'bed_to_id',
        'transferred_by',
        'transferred_at',
        'notes',
    ];

    protected function casts(): array
    {
        return ['transferred_at' => 'datetime'];
    }

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }

    public function wardFrom(): BelongsTo
    {
        return $this->belongsTo(Ward::class, 'ward_from_id');
    }

    public function bedFrom(): BelongsTo
    {
        return $this->belongsTo(Bed::class, 'bed_from_id');
    }

    public function wardTo(): BelongsTo
    {
        return $this->belongsTo(Ward::class, 'ward_to_id');
    }

    public function bedTo(): BelongsTo
    {
        return $this->belongsTo(Bed::class, 'bed_to_id');
    }

    public function transferredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'transferred_by');
    }
}
