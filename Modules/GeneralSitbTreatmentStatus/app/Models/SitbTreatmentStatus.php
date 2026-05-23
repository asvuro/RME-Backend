<?php

namespace Modules\GeneralSitbTreatmentStatus\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralSitbTreatmentStatus\Database\Factories\SitbTreatmentStatusFactory;

class SitbTreatmentStatus extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): SitbTreatmentStatusFactory
    {
        return SitbTreatmentStatusFactory::new();
    }
}