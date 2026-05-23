<?php

namespace Modules\GeneralSitbAnatomyClassification\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralSitbAnatomyClassification\Database\Factories\SitbAnatomyClassificationFactory;

class SitbAnatomyClassification extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): SitbAnatomyClassificationFactory
    {
        return SitbAnatomyClassificationFactory::new();
    }
}