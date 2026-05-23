<?php

namespace Modules\GeneralSitbArt\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralSitbArt\Database\Factories\SitbArtFactory;

class SitbArt extends Model
{
    use HasFactory;

    protected $table = 'sitb_arts';

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): SitbArtFactory
    {
        return SitbArtFactory::new();
    }
}