<?php

namespace Modules\InventorySterilizationCycle\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\InventorySterilizationCycle\Database\Factories\SterilizationCycleFactory;

/**
 * Satu siklus sterilisasi mesin CSSD (autoklaf dsb). status & biological_indicator_result
 * adalah gerbang untuk SterilizedItem::create — hanya cycle status=passed DAN
 * biological_indicator_result=negative yang boleh menghasilkan item steril
 * (lihat Modules\InventorySterilizationCycle\Services\SterilizedItemService).
 */
class SterilizationCycle extends Model
{
    use HasFactory;

    public const STATUS_IN_PROCESS = 'in_process';

    public const STATUS_PASSED = 'passed';

    public const STATUS_FAILED = 'failed';

    public const BI_PENDING = 'pending';

    public const BI_NEGATIVE = 'negative';

    public const BI_POSITIVE = 'positive';

    protected $fillable = [
        'cycle_number',
        'machine_name',
        'temperature_celsius',
        'pressure_bar',
        'duration_minutes',
        'started_at',
        'completed_at',
        'biological_indicator_result',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'temperature_celsius' => 'decimal:2',
            'pressure_bar' => 'decimal:2',
            'duration_minutes' => 'integer',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function sterilizedItems(): HasMany
    {
        return $this->hasMany(SterilizedItem::class, 'cycle_id');
    }

    /**
     * Format CYC-{tahun}-{urut 6 digit}, mengikuti pola generateVisitNumber()
     * di Modules\PendaftaranVisit\Models\Visit.
     */
    public static function generateCycleNumber(): string
    {
        $year = now()->format('Y');
        $count = static::query()->where('cycle_number', 'like', "CYC-{$year}-%")->count();

        return sprintf('CYC-%s-%06d', $year, $count + 1);
    }

    protected static function newFactory(): SterilizationCycleFactory
    {
        return SterilizationCycleFactory::new();
    }
}
