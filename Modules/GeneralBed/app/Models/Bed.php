<?php

namespace Modules\GeneralBed\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\AuditActivityLog\Support\Auditable;
use Modules\GeneralBed\Database\Factories\BedFactory;
use Modules\GeneralRoom\Models\Room;

/**
 * State machine bed, port master.ruang_kamar_tidur.STATUS simgos2 (referensi
 * jenis 20): available = kosong (1), reserved = dipesan (2), occupied = terisi
 * (3), maintenance = dibatalkan/tidak aktif (0). Perubahan status HANYA lewat
 * Modules\GeneralBed\Services\BedService — bukan tulisan langsung.
 */
class Bed extends Model
{
    use Auditable, HasFactory;

    public const STATUS_AVAILABLE = 'available';

    public const STATUS_RESERVED = 'reserved';

    public const STATUS_OCCUPIED = 'occupied';

    public const STATUS_MAINTENANCE = 'maintenance';

    protected $fillable = ['room_id', 'bed_number', 'is_active', 'status'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    protected static function newFactory(): BedFactory
    {
        return BedFactory::new();
    }
}
