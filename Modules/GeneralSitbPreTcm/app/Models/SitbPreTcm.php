<?php

namespace Modules\GeneralSitbPreTcm\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralSitbPreTcm\Database\Factories\SitbPreTcmFactory;

class SitbPreTcm extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): SitbPreTcmFactory
    {
        return SitbPreTcmFactory::new();
    }
}