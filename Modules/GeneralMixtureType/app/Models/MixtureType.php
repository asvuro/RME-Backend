<?php

namespace Modules\GeneralMixtureType\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralMixtureType\Database\Factories\MixtureTypeFactory;

class MixtureType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): MixtureTypeFactory
    {
        return MixtureTypeFactory::new();
    }
}