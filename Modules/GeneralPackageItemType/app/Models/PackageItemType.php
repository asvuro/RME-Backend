<?php

namespace Modules\GeneralPackageItemType\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralPackageItemType\Database\Factories\PackageItemTypeFactory;

class PackageItemType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): PackageItemTypeFactory
    {
        return PackageItemTypeFactory::new();
    }
}