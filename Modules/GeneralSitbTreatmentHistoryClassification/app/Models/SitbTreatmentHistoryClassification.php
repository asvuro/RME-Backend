<?php

namespace Modules\GeneralSitbTreatmentHistoryClassification\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralSitbTreatmentHistoryClassification\Database\Factories\SitbTreatmentHistoryClassificationFactory;

class SitbTreatmentHistoryClassification extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): SitbTreatmentHistoryClassificationFactory
    {
        return SitbTreatmentHistoryClassificationFactory::new();
    }
}