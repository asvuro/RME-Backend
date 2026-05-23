<?php

namespace Modules\GeneralAbsenceType\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\GeneralAbsenceType\Database\Factories\AbsenceTypeFactory;

class AbsenceType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function newFactory(): AbsenceTypeFactory
    {
        return AbsenceTypeFactory::new();
    }
}