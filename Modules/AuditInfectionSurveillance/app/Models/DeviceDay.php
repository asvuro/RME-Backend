<?php

namespace Modules\AuditInfectionSurveillance\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\AuditActivityLog\Support\Auditable;
use Modules\AuditInfectionSurveillance\Database\Factories\DeviceDayFactory;
use Modules\PendaftaranVisit\Models\Visit;

/**
 * Satu baris = satu masa pakai alat medis pada satu kunjungan. Denominator
 * surveilans PPI dihitung dari irisan [inserted_at, removed_at] dengan periode;
 * removed_at NULL berarti alat masih terpasang.
 */
class DeviceDay extends Model
{
    use Auditable, HasFactory;

    public const TYPE_KATETER_URINE = 'kateter_urine';

    public const TYPE_INFUS_IV = 'infus_iv';

    public const TYPE_VENTILATOR = 'ventilator';

    public const TYPES = [
        self::TYPE_KATETER_URINE,
        self::TYPE_INFUS_IV,
        self::TYPE_VENTILATOR,
    ];

    protected $fillable = ['visit_id', 'device_type', 'inserted_at', 'removed_at'];

    protected function casts(): array
    {
        return [
            'inserted_at' => 'datetime',
            'removed_at' => 'datetime',
        ];
    }

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }

    public function infectionCases(): HasMany
    {
        return $this->hasMany(InfectionCase::class, 'related_device_day_id');
    }

    protected static function newFactory(): DeviceDayFactory
    {
        return DeviceDayFactory::new();
    }
}
