<?php

namespace Modules\GeneralSitbTreatmentOutcome\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralSitbTreatmentOutcome\Database\Factories\SitbTreatmentOutcomeFactory;

class SitbTreatmentOutcome extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): SitbTreatmentOutcomeFactory
    {
        return SitbTreatmentOutcomeFactory::new();
    }
}