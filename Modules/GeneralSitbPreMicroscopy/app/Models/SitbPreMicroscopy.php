<?php

namespace Modules\GeneralSitbPreMicroscopy\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralSitbPreMicroscopy\Database\Factories\SitbPreMicroscopyFactory;

class SitbPreMicroscopy extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): SitbPreMicroscopyFactory
    {
        return SitbPreMicroscopyFactory::new();
    }
}