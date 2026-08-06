<?php

namespace Modules\AuditQualityIndicator\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\AuditActivityLog\Support\Auditable;
use Modules\AuditQualityIndicator\Database\Factories\QualityIndicatorFactory;

/**
 * Master Indikator Mutu (INM). achieved_value catatan capaian dihitung dari
 * numerator/denominator — target_value hanya pembanding tren, bukan angka
 * yang dipakai gerbang bisnis.
 */
class QualityIndicator extends Model
{
    use Auditable, HasFactory;

    public const CATEGORY_KLINIS = 'klinis';

    public const CATEGORY_MANAJERIAL = 'manajerial';

    public const CATEGORY_SASARAN_KESELAMATAN = 'sasaran_keselamatan';

    /** @var list<string> */
    public const CATEGORIES = [
        self::CATEGORY_KLINIS,
        self::CATEGORY_MANAJERIAL,
        self::CATEGORY_SASARAN_KESELAMATAN,
    ];

    protected $fillable = ['code', 'name', 'unit_of_measure', 'target_value', 'category'];

    protected function casts(): array
    {
        return ['target_value' => 'decimal:2'];
    }

    /** @return HasMany<QualityIndicatorRecord, $this> */
    public function records(): HasMany
    {
        return $this->hasMany(QualityIndicatorRecord::class, 'indicator_id');
    }

    protected static function newFactory(): QualityIndicatorFactory
    {
        return QualityIndicatorFactory::new();
    }
}
