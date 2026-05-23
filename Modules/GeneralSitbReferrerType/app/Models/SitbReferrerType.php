<?php

namespace Modules\GeneralSitbReferrerType\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralSitbReferrerType\Database\Factories\SitbReferrerTypeFactory;

class SitbReferrerType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): SitbReferrerTypeFactory
    {
        return SitbReferrerTypeFactory::new();
    }
}