<?php

namespace Modules\LayananImagingOrder\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\LayananImagingOrder\Database\Factories\ImagingStudyFactory;

/**
 * Studi hasil pengerjaan satu ImagingOrder. study_instance_uid sengaja nullable:
 * nilai sejatinya datang dari PACS nyata (masa depan); unique bila terisi karena
 * UID memang identitas global studi di dunia DICOM.
 */
class ImagingStudy extends Model
{
    use HasFactory;

    protected $table = 'imaging_studies';

    protected $fillable = [
        'imaging_order_id',
        'study_instance_uid',
        'performed_at',
        'findings_summary',
        'report_url',
    ];

    protected function casts(): array
    {
        return [
            'performed_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(ImagingOrder::class, 'imaging_order_id');
    }

    protected static function newFactory(): ImagingStudyFactory
    {
        return ImagingStudyFactory::new();
    }
}
