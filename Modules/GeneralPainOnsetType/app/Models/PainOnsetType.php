<?php

namespace Modules\GeneralPainOnsetType\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralPainOnsetType\Database\Factories\PainOnsetTypeFactory;

class PainOnsetType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): PainOnsetTypeFactory
    {
        return PainOnsetTypeFactory::new();
    }
}