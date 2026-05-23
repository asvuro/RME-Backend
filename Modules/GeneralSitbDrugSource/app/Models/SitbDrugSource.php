<?php

namespace Modules\GeneralSitbDrugSource\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralSitbDrugSource\Database\Factories\SitbDrugSourceFactory;

class SitbDrugSource extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): SitbDrugSourceFactory
    {
        return SitbDrugSourceFactory::new();
    }
}