<?php

namespace Modules\PegawaiRemunerasiJasaMedis\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\GeneralEmployee\Models\Employee;
use Modules\PegawaiRemunerasiJasaMedis\Database\Factories\RemunerationEntryFactory;

/**
 * Satu baris remunerasi jasa medis DPJP/operator per tindakan. net_amount
 * SELALU hasil kalkulasi RemunerationService — model tidak boleh ditulis
 * langsung utk field ini (lihat RemunerationService::calculateNet()).
 */
class RemunerationEntry extends Model
{
    use HasFactory;

    public const ROLE_OPERATOR_UTAMA = 'operator_utama';

    public const ROLE_ASISTEN = 'asisten';

    public const ROLE_ANESTESI = 'anestesi';

    protected $fillable = [
        'employee_id',
        'source_type',
        'source_id',
        'role',
        'gross_amount',
        'deduction_percentage',
        'fixed_deduction',
        'net_amount',
        'service_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'source_id' => 'integer',
            'gross_amount' => 'decimal:2',
            'deduction_percentage' => 'decimal:2',
            'fixed_deduction' => 'decimal:2',
            'net_amount' => 'decimal:2',
            'service_date' => 'date',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    protected static function newFactory(): RemunerationEntryFactory
    {
        return RemunerationEntryFactory::new();
    }
}
