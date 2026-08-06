<?php

namespace Modules\AuditInfectionSurveillance\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\AuditActivityLog\Support\Auditable;
use Modules\AuditInfectionSurveillance\Database\Factories\InfectionCaseFactory;
use Modules\PendaftaranVisit\Models\Visit;

/**
 * Kasus infeksi rumah sakit (PPI/HAIs). Jenis memakai istilah lokal:
 * ISK (infeksi saluran kemih), plebitis, IDO (infeksi daerah operasi),
 * VAP (ventilator-associated pneumonia).
 */
class InfectionCase extends Model
{
    use Auditable, HasFactory;

    public const TYPE_ISK = 'ISK';

    public const TYPE_PLEBITIS = 'plebitis';

    public const TYPE_IDO = 'IDO';

    public const TYPE_VAP = 'VAP';

    public const TYPES = [
        self::TYPE_ISK,
        self::TYPE_PLEBITIS,
        self::TYPE_IDO,
        self::TYPE_VAP,
    ];

    protected $fillable = ['visit_id', 'infection_type', 'diagnosed_at', 'related_device_day_id'];

    protected function casts(): array
    {
        return ['diagnosed_at' => 'datetime'];
    }

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }

    public function relatedDeviceDay(): BelongsTo
    {
        return $this->belongsTo(DeviceDay::class, 'related_device_day_id');
    }

    protected static function newFactory(): InfectionCaseFactory
    {
        return InfectionCaseFactory::new();
    }
}
