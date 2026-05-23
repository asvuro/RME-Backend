<?php

namespace Modules\GeneralSitbChildTbScore6\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralSitbChildTbScore6\Database\Factories\SitbChildTbScore6Factory;

class SitbChildTbScore6 extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): SitbChildTbScore6Factory
    {
        return SitbChildTbScore6Factory::new();
    }
}