<?php

namespace Modules\GeneralManufacturer\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralManufacturer\Database\Factories\ManufacturerFactory;

class Manufacturer extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): ManufacturerFactory
    {
        return ManufacturerFactory::new();
    }
}