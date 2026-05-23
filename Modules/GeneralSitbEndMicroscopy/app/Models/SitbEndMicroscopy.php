<?php

namespace Modules\GeneralSitbEndMicroscopy\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralSitbEndMicroscopy\Database\Factories\SitbEndMicroscopyFactory;

class SitbEndMicroscopy extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): SitbEndMicroscopyFactory
    {
        return SitbEndMicroscopyFactory::new();
    }
}