<?php

namespace Modules\GeneralSitbMonth3Microscopy\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralSitbMonth3Microscopy\Database\Factories\SitbMonth3MicroscopyFactory;

class SitbMonth3Microscopy extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): SitbMonth3MicroscopyFactory
    {
        return SitbMonth3MicroscopyFactory::new();
    }
}