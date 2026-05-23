<?php

namespace Modules\GeneralSitbMonth5Microscopy\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralSitbMonth5Microscopy\Database\Factories\SitbMonth5MicroscopyFactory;

class SitbMonth5Microscopy extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): SitbMonth5MicroscopyFactory
    {
        return SitbMonth5MicroscopyFactory::new();
    }
}