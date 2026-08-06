<?php

namespace Modules\AuditQualityIndicator\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\AuditQualityIndicator\Models\QualityIndicator;
use Modules\AuditQualityIndicator\Models\QualityIndicatorRecord;

class QualityIndicatorRecordFactory extends Factory
{
    protected $model = QualityIndicatorRecord::class;

    public function definition(): array
    {
        // unique() per bulan: satu indikator hanya boleh punya satu catatan
        // per bulan (constraint DB), jadi chain ->count(n) tidak boleh tabrakan.
        return [
            'indicator_id' => QualityIndicator::factory(),
            'period_month' => fake()->unique()->numberBetween(1, 12),
            'period_year' => now()->year,
            'numerator' => fake()->numberBetween(0, 100),
            'denominator' => fake()->numberBetween(101, 200),
            'recorded_by' => null,
        ];
    }
}
