<?php

namespace Modules\GeneralLaboratoryUnit\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralLaboratoryUnit\Database\Factories\LaboratoryUnitFactory;

class LaboratoryUnit extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): LaboratoryUnitFactory
    {
        return LaboratoryUnitFactory::new();
    }
}