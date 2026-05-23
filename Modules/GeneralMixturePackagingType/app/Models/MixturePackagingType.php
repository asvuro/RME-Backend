<?php

namespace Modules\GeneralMixturePackagingType\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralMixturePackagingType\Database\Factories\MixturePackagingTypeFactory;

class MixturePackagingType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): MixturePackagingTypeFactory
    {
        return MixturePackagingTypeFactory::new();
    }
}