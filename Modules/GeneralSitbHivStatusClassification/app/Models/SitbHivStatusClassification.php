<?php

namespace Modules\GeneralSitbHivStatusClassification\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralSitbHivStatusClassification\Database\Factories\SitbHivStatusClassificationFactory;

class SitbHivStatusClassification extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): SitbHivStatusClassificationFactory
    {
        return SitbHivStatusClassificationFactory::new();
    }
}