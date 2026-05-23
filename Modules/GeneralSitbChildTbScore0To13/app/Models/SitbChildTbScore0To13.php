<?php

namespace Modules\GeneralSitbChildTbScore0To13\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralSitbChildTbScore0To13\Database\Factories\SitbChildTbScore0To13Factory;

class SitbChildTbScore0To13 extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): SitbChildTbScore0To13Factory
    {
        return SitbChildTbScore0To13Factory::new();
    }
}