<?php

namespace Modules\GeneralSitbPpk\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralSitbPpk\Database\Factories\SitbPpkFactory;

class SitbPpk extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): SitbPpkFactory
    {
        return SitbPpkFactory::new();
    }
}