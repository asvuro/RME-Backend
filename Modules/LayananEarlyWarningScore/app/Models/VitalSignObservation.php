<?php

namespace Modules\LayananEarlyWarningScore\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\GeneralEmployee\Models\Employee;
use Modules\LayananEarlyWarningScore\Database\Factories\VitalSignObservationFactory;
use Modules\PendaftaranVisit\Models\Visit;

/**
 * Satu baris observasi tanda vital pasien + hasil skoring NEWS2 (Early Warning
 * Score). total_score/risk_level TIDAK pernah datang dari input klien: keduanya
 * dihitung dan diisi oleh Modules\LayananEarlyWarningScore\Services\EwsCalculatorService
 * lewat VitalSignObservationService saat store.
 */
class VitalSignObservation extends Model
{
    use HasFactory;

    public const CONSCIOUSNESS_ALERT = 'alert';

    public const CONSCIOUSNESS_VOICE = 'voice';

    public const CONSCIOUSNESS_PAIN = 'pain';

    public const CONSCIOUSNESS_UNRESPONSIVE = 'unresponsive';

    public const RISK_RENDAH = 'rendah';

    public const RISK_SEDANG = 'sedang';

    public const RISK_TINGGI = 'tinggi';

    protected $fillable = [
        'visit_id',
        'respiratory_rate',
        'spo2',
        'systolic_bp',
        'pulse_rate',
        'consciousness_level',
        'temperature_celsius',
        'recorded_by',
        'recorded_at',
        'total_score',
        'risk_level',
    ];

    protected function casts(): array
    {
        return [
            'recorded_at' => 'datetime',
            'temperature_celsius' => 'decimal:1',
        ];
    }

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }

    /** Pencatat observasi (pegawai, bukan user). */
    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'recorded_by');
    }

    protected static function newFactory(): VitalSignObservationFactory
    {
        return VitalSignObservationFactory::new();
    }
}
