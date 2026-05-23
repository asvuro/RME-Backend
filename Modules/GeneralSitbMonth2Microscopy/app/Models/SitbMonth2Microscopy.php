<?php

namespace Modules\GeneralSitbMonth2Microscopy\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralSitbMonth2Microscopy\Database\Factories\SitbMonth2MicroscopyFactory;

class SitbMonth2Microscopy extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): SitbMonth2MicroscopyFactory
    {
        return SitbMonth2MicroscopyFactory::new();
    }
}