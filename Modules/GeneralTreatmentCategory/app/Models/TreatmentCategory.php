<?php

namespace Modules\GeneralTreatmentCategory\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralTreatmentCategory\Database\Factories\TreatmentCategoryFactory;

class TreatmentCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): TreatmentCategoryFactory
    {
        return TreatmentCategoryFactory::new();
    }
}