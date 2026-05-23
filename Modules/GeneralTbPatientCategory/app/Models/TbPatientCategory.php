<?php

namespace Modules\GeneralTbPatientCategory\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralTbPatientCategory\Database\Factories\TbPatientCategoryFactory;

class TbPatientCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): TbPatientCategoryFactory
    {
        return TbPatientCategoryFactory::new();
    }
}