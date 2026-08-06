<?php

namespace Modules\LayananImagingOrder\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\GeneralEmployee\Models\Employee;
use Modules\LayananImagingOrder\Database\Factories\ImagingOrderFactory;
use Modules\PendaftaranVisit\Models\Visit;

/**
 * Order pemeriksaan imaging (PACS/DICOM adapter mode tracking): modul ini TIDAK
 * bicara protokol DICOM — hanya melacak pesanan + status pengerjaannya. Kolom
 * study_instance_uid di ImagingStudy adalah placeholder untuk integrasi PACS nyata.
 */
class ImagingOrder extends Model
{
    use HasFactory;

    protected $table = 'imaging_orders';

    public const STATUS_ORDERED = 'ordered';

    public const STATUS_SCHEDULED = 'scheduled';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    /**
     * Modality yang dikenali adapter tracking ini. Daftar tertutup sengaja
     * dipakai (bukan string bebas) supaya salah ketik seperti "XRAY"/"x-ray"
     * tidak menggagalkan pelaporan per modality; nilai baru cukup ditambah di sini.
     */
    public const MODALITIES = ['X-Ray', 'CT', 'MRI', 'USG'];

    protected $fillable = [
        'visit_id',
        'modality',
        'body_part',
        'ordered_by',
        'ordered_at',
        'scheduled_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'ordered_at' => 'datetime',
            'scheduled_at' => 'datetime',
        ];
    }

    public function visit(): BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }

    public function orderedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'ordered_by');
    }

    public function studies(): HasMany
    {
        return $this->hasMany(ImagingStudy::class);
    }

    protected static function newFactory(): ImagingOrderFactory
    {
        return ImagingOrderFactory::new();
    }
}
