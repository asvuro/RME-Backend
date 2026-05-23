<?php

namespace Modules\GeneralSitbDmTherapy\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralSitbDmTherapy\Database\Factories\SitbDmTherapyFactory;

class SitbDmTherapy extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): SitbDmTherapyFactory
    {
        return SitbDmTherapyFactory::new();
    }
}