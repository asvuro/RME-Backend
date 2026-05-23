<?php

namespace Modules\GeneralSitbChildTbScore5\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralSitbChildTbScore5\Database\Factories\SitbChildTbScore5Factory;

class SitbChildTbScore5 extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): SitbChildTbScore5Factory
    {
        return SitbChildTbScore5Factory::new();
    }
}