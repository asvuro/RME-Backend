<?php

namespace Modules\GeneralSitbOatGuideline\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralSitbOatGuideline\Database\Factories\SitbOatGuidelineFactory;

class SitbOatGuideline extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): SitbOatGuidelineFactory
    {
        return SitbOatGuidelineFactory::new();
    }
}