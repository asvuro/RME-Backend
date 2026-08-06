<?php

namespace Modules\AuditQualityIndicator\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\AuditActivityLog\Support\Auditable;
use Modules\AuditQualityIndicator\Database\Factories\QualityIndicatorRecordFactory;
use Modules\GeneralEmployee\Models\Employee;

/**
 * Catatan capaian INM bulanan untuk satu indikator. achieved_value adalah
 * atribut terhitung (bukan kolom): numerator/denominator*100, null bila
 * denominator <= 0 — tanpa penyebut, capaian tak terdefinisi, dan null
 * menjaga rata-rata tren tidak tertarik turun oleh bulan tanpa data.
 */
class QualityIndicatorRecord extends Model
{
    use Auditable, HasFactory;

    protected $fillable = ['indicator_id', 'period_month', 'period_year', 'numerator', 'denominator', 'recorded_by'];

    /** @var list<string> */
    protected $appends = ['achieved_value'];

    protected function casts(): array
    {
        return [
            'period_month' => 'integer',
            'period_year' => 'integer',
            'numerator' => 'decimal:2',
            'denominator' => 'decimal:2',
        ];
    }

    public function getAchievedValueAttribute(): ?float
    {
        $denominator = (float) $this->denominator;

        if ($denominator <= 0) {
            return null;
        }

        return round(((float) $this->numerator / $denominator) * 100, 2);
    }

    /** @return BelongsTo<QualityIndicator, $this> */
    public function indicator(): BelongsTo
    {
        return $this->belongsTo(QualityIndicator::class, 'indicator_id');
    }

    /** Petugas/pegawai yang mencatat capaian; bisa null bila profil pegawainya sudah dihapus. */
    public function recorder(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'recorded_by');
    }

    protected static function newFactory(): QualityIndicatorRecordFactory
    {
        return QualityIndicatorRecordFactory::new();
    }
}
